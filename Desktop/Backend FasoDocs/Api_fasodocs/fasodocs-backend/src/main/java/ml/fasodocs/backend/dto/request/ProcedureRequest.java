package ml.fasodocs.backend.dto.request;

import jakarta.validation.constraints.NotBlank;
import jakarta.validation.constraints.NotNull;
import lombok.Data;

import java.util.Set;

/**
 * DTO pour la création d'une procédure
 */
@Data
public class ProcedureRequest {

    @NotBlank(message = "Le nom est obligatoire")
    private String nom;

    @NotBlank(message = "Le titre est obligatoire")
    private String titre;

    private String urlVersFormulaire;

    @NotNull(message = "Le coût est obligatoire")
    private Integer cout;

    @NotBlank(message = "Le délai est obligatoire")
    private String delai;

    private String description;

    private Set<String> etapes;

    @NotNull(message = "La catégorie est obligatoire")
    private Long categorieId;
}
