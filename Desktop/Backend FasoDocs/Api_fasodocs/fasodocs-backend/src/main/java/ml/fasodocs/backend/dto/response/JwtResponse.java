package ml.fasodocs.backend.dto.response;

import lombok.AllArgsConstructor;
import lombok.Data;

/**
 * DTO pour la réponse JWT
 */
@Data
@AllArgsConstructor
public class JwtResponse {
    private String token;
    private String type = "Bearer";
    private Long id;
    private String nom;
    private String prenom;
    private String email;
    private String telephone;
    private String languePreferee;

    public JwtResponse(String token, Long id, String nom, String prenom, String email, String telephone, String languePreferee) {
        this.token = token;
        this.id = id;
        this.nom = nom;
        this.prenom = prenom;
        this.email = email;
        this.telephone = telephone;
        this.languePreferee = languePreferee;
    }
}
