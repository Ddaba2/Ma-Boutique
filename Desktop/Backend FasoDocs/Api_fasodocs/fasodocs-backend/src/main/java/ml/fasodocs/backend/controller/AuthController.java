package ml.fasodocs.backend.controller;

import io.swagger.v3.oas.annotations.Operation;
import io.swagger.v3.oas.annotations.tags.Tag;
import jakarta.validation.Valid;
import ml.fasodocs.backend.dto.request.ConnexionRequest;
import ml.fasodocs.backend.dto.request.InscriptionRequest;
import ml.fasodocs.backend.dto.request.MiseAJourProfilRequest;
import ml.fasodocs.backend.dto.response.JwtResponse;
import ml.fasodocs.backend.dto.response.MessageResponse;
import ml.fasodocs.backend.entity.Citoyen;
import ml.fasodocs.backend.service.AuthService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

/**
 * Contrôleur REST pour l'authentification et la gestion du profil
 */
@Tag(name = "Authentification", description = "API pour l'inscription, la connexion et la gestion du profil")
@RestController
@RequestMapping("/api/auth")
@CrossOrigin(origins = "*", maxAge = 3600)
public class AuthController {

    @Autowired
    private AuthService authService;

    /**
     * Inscription d'un nouveau citoyen
     */
    @Operation(summary = "Inscription d'un nouveau citoyen")
    @PostMapping("/inscription")
    public ResponseEntity<?> inscrire(@Valid @RequestBody InscriptionRequest request) {
        try {
            MessageResponse response = authService.inscrireCitoyen(request);
            if (response.isSuccess()) {
                return ResponseEntity.ok(response);
            } else {
                return ResponseEntity.badRequest().body(response);
            }
        } catch (Exception e) {
            return ResponseEntity.badRequest()
                    .body(MessageResponse.error("Erreur lors de l'inscription: " + e.getMessage()));
        }
    }

    /**
     * Connexion d'un citoyen
     */
    @Operation(summary = "Connexion d'un citoyen")
    @PostMapping("/connexion")
    public ResponseEntity<?> connecter(@Valid @RequestBody ConnexionRequest request) {
        try {
            JwtResponse response = authService.connecterCitoyen(request);
            return ResponseEntity.ok(response);
        } catch (Exception e) {
            return ResponseEntity.badRequest()
                    .body(MessageResponse.error("Erreur de connexion: " + e.getMessage()));
        }
    }

    /**
     * Vérification de l'email
     */
    @Operation(summary = "Vérification de l'email via code")
    @GetMapping("/verify")
    public ResponseEntity<?> verifierEmail(@RequestParam String code) {
        try {
            MessageResponse response = authService.verifierEmail(code);
            return ResponseEntity.ok(response);
        } catch (Exception e) {
            return ResponseEntity.badRequest()
                    .body(MessageResponse.error("Code de vérification invalide"));
        }
    }

    /**
     * Récupération du profil du citoyen connecté
     */
    @Operation(summary = "Récupération du profil du citoyen connecté")
    @GetMapping("/profil")
    public ResponseEntity<?> obtenirProfil() {
        try {
            Citoyen citoyen = authService.getProfilCitoyenConnecte();
            return ResponseEntity.ok(citoyen);
        } catch (Exception e) {
            return ResponseEntity.badRequest()
                    .body(MessageResponse.error("Erreur lors de la récupération du profil: " + e.getMessage()));
        }
    }

    /**
     * Mise à jour du profil
     */
    @Operation(summary = "Mise à jour du profil du citoyen connecté")
    @PutMapping("/profil")
    public ResponseEntity<?> mettreAJourProfil(@Valid @RequestBody MiseAJourProfilRequest request) {
        try {
            MessageResponse response = authService.mettreAJourProfil(request);
            return ResponseEntity.ok(response);
        } catch (Exception e) {
            return ResponseEntity.badRequest()
                    .body(MessageResponse.error("Erreur lors de la mise à jour du profil: " + e.getMessage()));
        }
    }

    /**
     * Déconnexion
     */
    @Operation(summary = "Déconnexion du citoyen")
    @PostMapping("/deconnexion")
    public ResponseEntity<?> deconnecter() {
        try {
            MessageResponse response = authService.deconnecter();
            return ResponseEntity.ok(response);
        } catch (Exception e) {
            return ResponseEntity.badRequest()
                    .body(MessageResponse.error("Erreur lors de la déconnexion: " + e.getMessage()));
        }
    }
}
