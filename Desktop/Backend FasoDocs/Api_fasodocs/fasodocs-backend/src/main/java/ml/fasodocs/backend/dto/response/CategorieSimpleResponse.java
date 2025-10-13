package ml.fasodocs.backend.dto.response;

import lombok.Data;

/**
 * DTO simple pour la catégorie (utilisé dans les réponses imbriquées)
 */
@Data
public class CategorieSimpleResponse {
    private Long id;
    private String titre;
    private String description;
    private String iconeUrl;
}
