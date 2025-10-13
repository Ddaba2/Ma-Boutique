package ml.fasodocs.backend.repository;

import ml.fasodocs.backend.entity.HistoriqueLog;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

import java.time.LocalDateTime;
import java.util.List;

/**
 * Repository pour l'entité HistoriqueLog
 */
@Repository
public interface HistoriqueLogRepository extends JpaRepository<HistoriqueLog, Long> {

    /**
     * Recherche l'historique d'un citoyen
     */
    List<HistoriqueLog> findByCitoyenIdOrderByDateActionDesc(Long citoyenId);

    /**
     * Recherche l'historique par type d'action
     */
    List<HistoriqueLog> findByTypeAction(String typeAction);

    /**
     * Recherche l'historique d'un citoyen pour une procédure spécifique
     */
    List<HistoriqueLog> findByCitoyenIdAndProcedureId(Long citoyenId, Long procedureId);

    /**
     * Recherche l'historique entre deux dates
     */
    List<HistoriqueLog> findByDateActionBetween(LocalDateTime dateDebut, LocalDateTime dateFin);
}
