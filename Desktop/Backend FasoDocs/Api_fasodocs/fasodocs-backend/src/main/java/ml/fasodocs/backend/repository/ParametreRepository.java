package ml.fasodocs.backend.repository;

import ml.fasodocs.backend.entity.Parametre;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

import java.util.Optional;

/**
 * Repository pour l'entité Parametre
 */
@Repository
public interface ParametreRepository extends JpaRepository<Parametre, Long> {

    /**
     * Recherche un paramètre par son contenu (clé)
     */
    Optional<Parametre> findByContenu(String contenu);

    /**
     * Vérifie si un paramètre existe
     */
    boolean existsByContenu(String contenu);
}
