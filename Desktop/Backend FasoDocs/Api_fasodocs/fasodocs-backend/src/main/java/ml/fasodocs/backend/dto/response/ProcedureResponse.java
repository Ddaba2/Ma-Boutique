package ml.fasodocs.backend.dto.response;

import lombok.Data;

import java.time.LocalDateTime;
import java.util.Set;

/**
 * DTO pour la réponse d'une procédure
 */
@Data
public class ProcedureResponse {
    private Long id;
    private String nom;
    private String titre;
    private String urlVersFormulaire;
    private Integer cout;
    private String delai;
    private String description;
    private Set<String> etapes;
    private LocalDateTime dateCreation;
    private LocalDateTime dateModification;
    private CategorieSimpleResponse categorie;
    private Set<DocumentRequisResponse> documentsRequis;
    private Set<LieuDeTraitementResponse> lieux;
}
