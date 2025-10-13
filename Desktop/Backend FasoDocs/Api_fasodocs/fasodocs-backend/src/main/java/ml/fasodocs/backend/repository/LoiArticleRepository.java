package ml.fasodocs.backend.repository;

import ml.fasodocs.backend.entity.LoiArticle;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

import java.util.List;

/**
 * Repository pour l'entité LoiArticle
 */
@Repository
public interface LoiArticleRepository extends JpaRepository<LoiArticle, Long> {

    /**
     * Recherche les lois/articles associés à une procédure
     */
    List<LoiArticle> findByProcedureId(Long procedureId);
}
