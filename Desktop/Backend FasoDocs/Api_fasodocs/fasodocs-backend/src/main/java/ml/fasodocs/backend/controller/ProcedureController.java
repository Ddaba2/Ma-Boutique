package ml.fasodocs.backend.controller;

import io.swagger.v3.oas.annotations.Operation;
import io.swagger.v3.oas.annotations.tags.Tag;
import jakarta.validation.Valid;
import ml.fasodocs.backend.dto.request.ProcedureRequest;
import ml.fasodocs.backend.dto.response.MessageResponse;
import ml.fasodocs.backend.dto.response.ProcedureResponse;
import ml.fasodocs.backend.service.ProcedureService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.ResponseEntity;
import org.springframework.security.access.prepost.PreAuthorize;
import org.springframework.web.bind.annotation.*;

import java.util.List;

/**
 * Contrôleur REST pour la gestion des procédures administratives
 */
@Tag(name = "Procédures", description = "API pour la gestion des procédures administratives")
@RestController
@RequestMapping("/api/procedures")
@CrossOrigin(origins = "*", maxAge = 3600)
public class ProcedureController {

    @Autowired
    private ProcedureService procedureService;

    /**
     * Récupère toutes les procédures
     */
    @Operation(summary = "Récupère toutes les procédures")
    @GetMapping
    public ResponseEntity<List<ProcedureResponse>> obtenirToutesProcedures() {
        return ResponseEntity.ok(procedureService.obtenirToutesProcedures());
    }

    /**
     * Récupère une procédure par ID
     */
    @Operation(summary = "Récupère une procédure par son ID")
    @GetMapping("/{id}")
    public ResponseEntity<?> obtenirProcedureParId(@PathVariable Long id) {
        try {
            ProcedureResponse response = procedureService.obtenirProcedureParId(id);
            return ResponseEntity.ok(response);
        } catch (Exception e) {
            return ResponseEntity.badRequest()
                    .body(MessageResponse.error("Procédure non trouvée: " + e.getMessage()));
        }
    }

    /**
     * Récupère les procédures par catégorie
     */
    @Operation(summary = "Récupère les procédures d'une catégorie")
    @GetMapping("/categorie/{categorieId}")
    public ResponseEntity<List<ProcedureResponse>> obtenirProceduresParCategorie(@PathVariable Long categorieId) {
        return ResponseEntity.ok(procedureService.obtenirProceduresParCategorie(categorieId));
    }

    /**
     * Recherche des procédures
     */
    @Operation(summary = "Recherche des procédures par nom ou titre")
    @GetMapping("/rechercher")
    public ResponseEntity<List<ProcedureResponse>> rechercherProcedures(@RequestParam String q) {
        return ResponseEntity.ok(procedureService.rechercherProcedures(q));
    }

    /**
     * Crée une nouvelle procédure (Admin uniquement)
     */
    @Operation(summary = "Crée une nouvelle procédure (Admin)")
    @PreAuthorize("hasRole('ADMIN')")
    @PostMapping
    public ResponseEntity<?> creerProcedure(@Valid @RequestBody ProcedureRequest request) {
        try {
            ProcedureResponse response = procedureService.creerProcedure(request);
            return ResponseEntity.ok(response);
        } catch (Exception e) {
            return ResponseEntity.badRequest()
                    .body(MessageResponse.error("Erreur lors de la création: " + e.getMessage()));
        }
    }

    /**
     * Met à jour une procédure (Admin uniquement)
     */
    @Operation(summary = "Met à jour une procédure (Admin)")
    @PreAuthorize("hasRole('ADMIN')")
    @PutMapping("/{id}")
    public ResponseEntity<?> mettreAJourProcedure(@PathVariable Long id, 
                                                  @Valid @RequestBody ProcedureRequest request) {
        try {
            ProcedureResponse response = procedureService.mettreAJourProcedure(id, request);
            return ResponseEntity.ok(response);
        } catch (Exception e) {
            return ResponseEntity.badRequest()
                    .body(MessageResponse.error("Erreur lors de la mise à jour: " + e.getMessage()));
        }
    }

    /**
     * Supprime une procédure (Admin uniquement)
     */
    @Operation(summary = "Supprime une procédure (Admin)")
    @PreAuthorize("hasRole('ADMIN')")
    @DeleteMapping("/{id}")
    public ResponseEntity<?> supprimerProcedure(@PathVariable Long id) {
        try {
            MessageResponse response = procedureService.supprimerProcedure(id);
            return ResponseEntity.ok(response);
        } catch (Exception e) {
            return ResponseEntity.badRequest()
                    .body(MessageResponse.error("Erreur lors de la suppression: " + e.getMessage()));
        }
    }
}
