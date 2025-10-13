package ml.fasodocs.backend.repository;

import ml.fasodocs.backend.entity.Traduction;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

import java.util.List;
import java.util.Optional;

/**
 * Repository pour l'entité Traduction
 */
@Repository
public interface TraductionRepository extends JpaRepository<Traduction, Long> {

    /**
     * Recherche les traductions pour une procédure
     */
    List<Traduction> findByProcedureId(Long procedureId);

    /**
     * Recherche une traduction spécifique pour une procédure et une langue
     */
    Optional<Traduction> findByProcedureIdAndLangue(Long procedureId, String langue);

    /**
     * Recherche toutes les traductions dans une langue spécifique
     */
    List<Traduction> findByLangue(String langue);

    /**
     * Vérifie si une traduction existe pour une procédure dans une langue
     */
    boolean existsByProcedureIdAndLangue(Long procedureId, String langue);
}
