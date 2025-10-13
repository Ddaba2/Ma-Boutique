package ml.fasodocs.backend.repository;

import ml.fasodocs.backend.entity.Categorie;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

import java.util.Optional;

/**
 * Repository pour l'entité Categorie
 */
@Repository
public interface CategorieRepository extends JpaRepository<Categorie, Long> {

    /**
     * Recherche une catégorie par titre
     */
    Optional<Categorie> findByTitre(String titre);

    /**
     * Vérifie si une catégorie existe par titre
     */
    boolean existsByTitre(String titre);
}
