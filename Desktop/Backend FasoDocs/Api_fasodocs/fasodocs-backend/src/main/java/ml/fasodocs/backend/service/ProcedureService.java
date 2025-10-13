package ml.fasodocs.backend.service;

import ml.fasodocs.backend.dto.request.ProcedureRequest;
import ml.fasodocs.backend.dto.response.*;
import ml.fasodocs.backend.entity.*;
import ml.fasodocs.backend.repository.*;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.util.List;
import java.util.stream.Collectors;

/**
 * Service pour la gestion des procédures administratives
 */
@Service
@Transactional
public class ProcedureService {

    private static final Logger logger = LoggerFactory.getLogger(ProcedureService.class);

    @Autowired
    private ProcedureRepository procedureRepository;

    @Autowired
    private CategorieRepository categorieRepository;

    @Autowired
    private NotificationService notificationService;

    @Autowired
    private HistoriqueService historiqueService;

    /**
     * Récupère toutes les procédures
     */
    public List<ProcedureResponse> obtenirToutesProcedures() {
        return procedureRepository.findAll().stream()
                .map(this::convertirEnResponse)
                .collect(Collectors.toList());
    }

    /**
     * Récupère une procédure par ID
     */
    public ProcedureResponse obtenirProcedureParId(Long id) {
        Procedure procedure = procedureRepository.findById(id)
                .orElseThrow(() -> new RuntimeException("Procédure non trouvée"));
        
        // Enregistrer l'historique de consultation
        historiqueService.enregistrerConsultation(procedure);
        
        return convertirEnResponse(procedure);
    }

    /**
     * Récupère les procédures par catégorie
     */
    public List<ProcedureResponse> obtenirProceduresParCategorie(Long categorieId) {
        return procedureRepository.findByCategorieId(categorieId).stream()
                .map(this::convertirEnResponse)
                .collect(Collectors.toList());
    }

    /**
     * Recherche des procédures par nom
     */
    public List<ProcedureResponse> rechercherProcedures(String recherche) {
        List<Procedure> procedures = procedureRepository.rechercherParNom(recherche);
        procedures.addAll(procedureRepository.rechercherParTitre(recherche));
        
        return procedures.stream()
                .distinct()
                .map(this::convertirEnResponse)
                .collect(Collectors.toList());
    }

    /**
     * Crée une nouvelle procédure (Admin)
     */
    public ProcedureResponse creerProcedure(ProcedureRequest request) {
        Categorie categorie = categorieRepository.findById(request.getCategorieId())
                .orElseThrow(() -> new RuntimeException("Catégorie non trouvée"));

        Procedure procedure = new Procedure();
        procedure.setNom(request.getNom());
        procedure.setTitre(request.getTitre());
        procedure.setUrlVersFormulaire(request.getUrlVersFormulaire());
        procedure.setCout(request.getCout());
        procedure.setDelai(request.getDelai());
        procedure.setDescription(request.getDescription());
        procedure.setEtapes(request.getEtapes());
        procedure.setCategorie(categorie);

        Procedure savedProcedure = procedureRepository.save(procedure);
        
        logger.info("Nouvelle procédure créée: {}", savedProcedure.getNom());

        return convertirEnResponse(savedProcedure);
    }

    /**
     * Met à jour une procédure (Admin)
     */
    public ProcedureResponse mettreAJourProcedure(Long id, ProcedureRequest request) {
        Procedure procedure = procedureRepository.findById(id)
                .orElseThrow(() -> new RuntimeException("Procédure non trouvée"));

        // Sauvegarder les anciennes valeurs pour détecter les changements
        Integer ancienCout = procedure.getCout();
        String ancienDelai = procedure.getDelai();

        procedure.setNom(request.getNom());
        procedure.setTitre(request.getTitre());
        procedure.setUrlVersFormulaire(request.getUrlVersFormulaire());
        procedure.setCout(request.getCout());
        procedure.setDelai(request.getDelai());
        procedure.setDescription(request.getDescription());
        procedure.setEtapes(request.getEtapes());

        if (request.getCategorieId() != null) {
            Categorie categorie = categorieRepository.findById(request.getCategorieId())
                    .orElseThrow(() -> new RuntimeException("Catégorie non trouvée"));
            procedure.setCategorie(categorie);
        }

        Procedure updatedProcedure = procedureRepository.save(procedure);

        // Envoyer des notifications si des attributs clés ont changé
        if (!ancienCout.equals(procedure.getCout()) || !ancienDelai.equals(procedure.getDelai())) {
            notificationService.notifierMiseAJourProcedure(updatedProcedure);
        }

        logger.info("Procédure mise à jour: {}", updatedProcedure.getNom());

        return convertirEnResponse(updatedProcedure);
    }

    /**
     * Supprime une procédure (Admin)
     */
    public MessageResponse supprimerProcedure(Long id) {
        Procedure procedure = procedureRepository.findById(id)
                .orElseThrow(() -> new RuntimeException("Procédure non trouvée"));

        procedureRepository.delete(procedure);
        
        logger.info("Procédure supprimée: {}", procedure.getNom());

        return MessageResponse.success("Procédure supprimée avec succès!");
    }

    /**
     * Convertit une entité Procedure en ProcedureResponse
     */
    private ProcedureResponse convertirEnResponse(Procedure procedure) {
        ProcedureResponse response = new ProcedureResponse();
        response.setId(procedure.getId());
        response.setNom(procedure.getNom());
        response.setTitre(procedure.getTitre());
        response.setUrlVersFormulaire(procedure.getUrlVersFormulaire());
        response.setCout(procedure.getCout());
        response.setDelai(procedure.getDelai());
        response.setDescription(procedure.getDescription());
        response.setEtapes(procedure.getEtapes());
        response.setDateCreation(procedure.getDateCreation());
        response.setDateModification(procedure.getDateModification());

        // Catégorie
        CategorieSimpleResponse categorieResponse = new CategorieSimpleResponse();
        categorieResponse.setId(procedure.getCategorie().getId());
        categorieResponse.setTitre(procedure.getCategorie().getTitre());
        categorieResponse.setDescription(procedure.getCategorie().getDescription());
        categorieResponse.setIconeUrl(procedure.getCategorie().getIconeUrl());
        response.setCategorie(categorieResponse);

        // Documents requis
        response.setDocumentsRequis(
            procedure.getDocumentsRequis().stream()
                .map(this::convertirDocumentEnResponse)
                .collect(Collectors.toSet())
        );

        // Lieux de traitement
        response.setLieux(
            procedure.getLieux().stream()
                .map(this::convertirLieuEnResponse)
                .collect(Collectors.toSet())
        );

        return response;
    }

    private DocumentRequisResponse convertirDocumentEnResponse(DocumentRequis document) {
        DocumentRequisResponse response = new DocumentRequisResponse();
        response.setId(document.getId());
        response.setDescription(document.getDescription());
        response.setEstObligatoire(document.getEstObligatoire());
        response.setModeleUrl(document.getModeleUrl());
        return response;
    }

    private LieuDeTraitementResponse convertirLieuEnResponse(LieuDeTraitement lieu) {
        LieuDeTraitementResponse response = new LieuDeTraitementResponse();
        response.setId(lieu.getId());
        response.setNom(lieu.getNom());
        response.setAdresse(lieu.getAdresse());
        response.setHoraires(lieu.getHoraires());
        response.setCoordonneesGPS(lieu.getCoordonneesGPS());
        response.setLatitude(lieu.getLatitude());
        response.setLongitude(lieu.getLongitude());
        response.setTelephone(lieu.getTelephone());
        response.setEmail(lieu.getEmail());
        return response;
    }
}
