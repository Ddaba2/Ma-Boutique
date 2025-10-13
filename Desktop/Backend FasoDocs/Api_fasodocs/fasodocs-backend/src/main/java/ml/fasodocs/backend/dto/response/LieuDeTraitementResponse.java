package ml.fasodocs.backend.dto.response;

import lombok.Data;

/**
 * DTO pour la réponse d'un lieu de traitement
 */
@Data
public class LieuDeTraitementResponse {
    private Long id;
    private String nom;
    private String adresse;
    private String horaires;
    private String coordonneesGPS;
    private Double latitude;
    private Double longitude;
    private String telephone;
    private String email;
}
