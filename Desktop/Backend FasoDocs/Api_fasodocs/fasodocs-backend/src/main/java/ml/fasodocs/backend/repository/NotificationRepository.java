package ml.fasodocs.backend.repository;

import ml.fasodocs.backend.entity.Notification;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.data.jpa.repository.Query;
import org.springframework.data.repository.query.Param;
import org.springframework.stereotype.Repository;

import java.util.List;

/**
 * Repository pour l'entité Notification
 */
@Repository
public interface NotificationRepository extends JpaRepository<Notification, Long> {

    /**
     * Recherche toutes les notifications d'un citoyen
     */
    List<Notification> findByCitoyenIdOrderByDateEnvoiDesc(Long citoyenId);

    /**
     * Recherche les notifications non lues d'un citoyen
     */
    List<Notification> findByCitoyenIdAndEstLueFalseOrderByDateEnvoiDesc(Long citoyenId);

    /**
     * Compte les notifications non lues d'un citoyen
     */
    Long countByCitoyenIdAndEstLueFalse(Long citoyenId);

    /**
     * Recherche les notifications par type
     */
    List<Notification> findByType(String type);

    /**
     * Recherche les notifications pour une procédure spécifique
     */
    List<Notification> findByProcedureId(Long procedureId);

    /**
     * Supprime les anciennes notifications lues
     */
    @Query("DELETE FROM Notification n WHERE n.estLue = true AND n.dateEnvoi < :dateLimite")
    void supprimerAnciennesNotificationsLues(@Param("dateLimite") java.time.LocalDateTime dateLimite);
}
