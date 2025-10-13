package ml.fasodocs.backend.service;

import ml.fasodocs.backend.dto.request.ConnexionRequest;
import ml.fasodocs.backend.dto.request.InscriptionRequest;
import ml.fasodocs.backend.dto.request.MiseAJourProfilRequest;
import ml.fasodocs.backend.dto.response.JwtResponse;
import ml.fasodocs.backend.dto.response.MessageResponse;
import ml.fasodocs.backend.entity.Citoyen;
import ml.fasodocs.backend.entity.Role;
import ml.fasodocs.backend.repository.CitoyenRepository;
import ml.fasodocs.backend.repository.RoleRepository;
import ml.fasodocs.backend.security.JwtUtils;
import ml.fasodocs.backend.security.UserDetailsImpl;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.security.authentication.AuthenticationManager;
import org.springframework.security.authentication.UsernamePasswordAuthenticationToken;
import org.springframework.security.core.Authentication;
import org.springframework.security.core.context.SecurityContextHolder;
import org.springframework.security.crypto.password.PasswordEncoder;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.util.*;

/**
 * Service pour la gestion de l'authentification et des citoyens
 */
@Service
@Transactional
public class AuthService {

    private static final Logger logger = LoggerFactory.getLogger(AuthService.class);

    @Autowired
    private AuthenticationManager authenticationManager;

    @Autowired
    private CitoyenRepository citoyenRepository;

    @Autowired
    private RoleRepository roleRepository;

    @Autowired
    private PasswordEncoder encoder;

    @Autowired
    private JwtUtils jwtUtils;

    @Autowired
    private EmailService emailService;

    /**
     * Inscription d'un nouveau citoyen
     */
    public MessageResponse inscrireCitoyen(InscriptionRequest request) {
        // Vérifier si l'email existe déjà
        if (request.getEmail() != null && citoyenRepository.existsByEmail(request.getEmail())) {
            return MessageResponse.error("Erreur: L'email est déjà utilisé!");
        }

        // Vérifier si le téléphone existe déjà
        if (request.getTelephone() != null && citoyenRepository.existsByTelephone(request.getTelephone())) {
            return MessageResponse.error("Erreur: Le téléphone est déjà utilisé!");
        }

        // Au moins un email ou téléphone doit être fourni
        if (request.getEmail() == null && request.getTelephone() == null) {
            return MessageResponse.error("Erreur: Email ou téléphone requis!");
        }

        // Créer le nouveau citoyen
        Citoyen citoyen = new Citoyen();
        citoyen.setNom(request.getNom());
        citoyen.setPrenom(request.getPrenom());
        citoyen.setEmail(request.getEmail());
        citoyen.setTelephone(request.getTelephone());
        citoyen.setMotDePasse(encoder.encode(request.getMotDePasse()));
        citoyen.setLanguePreferee(request.getLanguePreferee());
        citoyen.setEstActif(true);
        citoyen.setEmailVerifie(false);

        // Générer un code de vérification
        String codeVerification = UUID.randomUUID().toString();
        citoyen.setCodeVerification(codeVerification);

        // Attribuer le rôle CITOYEN par défaut
        Role roleCitoyen = roleRepository.findByNom(Role.NomRole.ROLE_CITOYEN)
                .orElseThrow(() -> new RuntimeException("Erreur: Rôle non trouvé."));
        
        Set<Role> roles = new HashSet<>();
        roles.add(roleCitoyen);
        citoyen.setRoles(roles);

        // Sauvegarder le citoyen
        citoyenRepository.save(citoyen);

        // Envoyer l'email de confirmation
        if (citoyen.getEmail() != null) {
            emailService.envoyerEmailVerification(citoyen.getEmail(), codeVerification);
        }

        logger.info("Nouveau citoyen inscrit: {} {}", citoyen.getNom(), citoyen.getPrenom());

        return MessageResponse.success("Inscription réussie! Veuillez vérifier votre email.");
    }

    /**
     * Connexion d'un citoyen
     */
    public JwtResponse connecterCitoyen(ConnexionRequest request) {
        Authentication authentication = authenticationManager.authenticate(
                new UsernamePasswordAuthenticationToken(request.getIdentifiant(), request.getMotDePasse()));

        SecurityContextHolder.getContext().setAuthentication(authentication);
        String jwt = jwtUtils.generateJwtToken(authentication);

        UserDetailsImpl userDetails = (UserDetailsImpl) authentication.getPrincipal();

        // Mettre à jour le token FCM si fourni
        if (request.getTokenFcm() != null) {
            Citoyen citoyen = citoyenRepository.findById(userDetails.getId())
                    .orElseThrow(() -> new RuntimeException("Citoyen non trouvé"));
            citoyen.setTokenFcm(request.getTokenFcm());
            citoyenRepository.save(citoyen);
        }

        logger.info("Citoyen connecté: {}", userDetails.getUsername());

        return new JwtResponse(
                jwt,
                userDetails.getId(),
                userDetails.getNom(),
                userDetails.getPrenom(),
                userDetails.getEmail(),
                userDetails.getTelephone(),
                citoyenRepository.findById(userDetails.getId()).get().getLanguePreferee()
        );
    }

    /**
     * Vérification de l'email
     */
    public MessageResponse verifierEmail(String code) {
        Citoyen citoyen = citoyenRepository.findByCodeVerification(code)
                .orElseThrow(() -> new RuntimeException("Code de vérification invalide"));

        citoyen.setEmailVerifie(true);
        citoyen.setCodeVerification(null);
        citoyenRepository.save(citoyen);

        logger.info("Email vérifié pour: {} {}", citoyen.getNom(), citoyen.getPrenom());

        return MessageResponse.success("Email vérifié avec succès!");
    }

    /**
     * Récupération du profil du citoyen connecté
     */
    public Citoyen getProfilCitoyenConnecte() {
        Authentication authentication = SecurityContextHolder.getContext().getAuthentication();
        UserDetailsImpl userDetails = (UserDetailsImpl) authentication.getPrincipal();

        return citoyenRepository.findById(userDetails.getId())
                .orElseThrow(() -> new RuntimeException("Citoyen non trouvé"));
    }

    /**
     * Mise à jour du profil
     */
    public MessageResponse mettreAJourProfil(MiseAJourProfilRequest request) {
        Citoyen citoyen = getProfilCitoyenConnecte();

        citoyen.setNom(request.getNom());
        citoyen.setPrenom(request.getPrenom());
        
        if (request.getTelephone() != null) {
            citoyen.setTelephone(request.getTelephone());
        }
        
        if (request.getLanguePreferee() != null) {
            citoyen.setLanguePreferee(request.getLanguePreferee());
        }

        citoyenRepository.save(citoyen);

        logger.info("Profil mis à jour pour: {} {}", citoyen.getNom(), citoyen.getPrenom());

        return MessageResponse.success("Profil mis à jour avec succès!");
    }

    /**
     * Déconnexion (supprimer le token FCM)
     */
    public MessageResponse deconnecter() {
        Citoyen citoyen = getProfilCitoyenConnecte();
        citoyen.setTokenFcm(null);
        citoyenRepository.save(citoyen);

        logger.info("Citoyen déconnecté: {} {}", citoyen.getNom(), citoyen.getPrenom());

        return MessageResponse.success("Déconnexion réussie!");
    }
}
