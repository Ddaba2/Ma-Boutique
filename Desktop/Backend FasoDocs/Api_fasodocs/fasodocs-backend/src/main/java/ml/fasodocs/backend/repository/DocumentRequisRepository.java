package ml.fasodocs.backend.repository;

import ml.fasodocs.backend.entity.DocumentRequis;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

import java.util.List;

/**
 * Repository pour l'entité DocumentRequis
 */
@Repository
public interface DocumentRequisRepository extends JpaRepository<DocumentRequis, Long> {

    /**
     * Recherche tous les documents requis pour une procédure
     */
    List<DocumentRequis> findByProcedureId(Long procedureId);

    /**
     * Recherche tous les documents obligatoires pour une procédure
     */
    List<DocumentRequis> findByProcedureIdAndEstObligatoireTrue(Long procedureId);
}
