package ml.fasodocs.backend.repository;

import ml.fasodocs.backend.entity.Citoyen;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.query.Param;
import org.springframework.stereotype.Repository;

import java.util.Optional;

/**
 * Repository pour l'entité Citoyen
 */
@Repository
public interface CitoyenRepository extends JpaRepository<Citoyen, Long> {

    /**
     * Recherche un citoyen par email
     */
    Optional<Citoyen> findByEmail(String email);

    /**
     * Recherche un citoyen par téléphone
     */
    Optional<Citoyen> findByTelephone(String telephone);

    /**
     * Vérifie si un email existe déjà
     */
    boolean existsByEmail(String email);

    /**
     * Vérifie si un téléphone existe déjà
     */
    boolean existsByTelephone(String telephone);

    /**
     * Recherche un citoyen par code de vérification
     */
    Optional<Citoyen> findByCodeVerification(String codeVerification);

    /**
     * Recherche tous les citoyens actifs
     */
    @Query("SELECT c FROM Citoyen c WHERE c.estActif = true")
    java.util.List<Citoyen> findAllActifs();

    /**
     * Recherche un citoyen par email ou téléphone
     */
    @Query("SELECT c FROM Citoyen c WHERE c.email = :identifiant OR c.telephone = :identifiant")
    Optional<Citoyen> findByEmailOrTelephone(@Param("identifiant") String identifiant);
}
