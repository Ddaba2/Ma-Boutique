package ml.fasodocs.backend.dto.request;

import jakarta.validation.constraints.NotBlank;
import lombok.Data;

/**
 * DTO pour la demande de connexion
 */
@Data
public class ConnexionRequest {

    @NotBlank(message = "L'identifiant est obligatoire (email ou téléphone)")
    private String identifiant; // Email ou téléphone

    @NotBlank(message = "Le mot de passe est obligatoire")
    private String motDePasse;

    private String tokenFcm; // Token pour les notifications push
}
