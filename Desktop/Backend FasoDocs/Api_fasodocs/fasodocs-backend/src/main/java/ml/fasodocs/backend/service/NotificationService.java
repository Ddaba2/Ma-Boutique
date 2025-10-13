package ml.fasodocs.backend.service;

import ml.fasodocs.backend.dto.response.NotificationResponse;
import ml.fasodocs.backend.entity.Citoyen;
import ml.fasodocs.backend.entity.Notification;
import ml.fasodocs.backend.entity.Procedure;
import ml.fasodocs.backend.repository.CitoyenRepository;
import ml.fasodocs.backend.repository.NotificationRepository;
import ml.fasodocs.backend.security.UserDetailsImpl;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.security.core.Authentication;
import org.springframework.security.core.context.SecurityContextHolder;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.util.List;
import java.util.stream.Collectors;

/**
 * Service pour la gestion des notifications
 */
@Service
@Transactional
public class NotificationService {

    private static final Logger logger = LoggerFactory.getLogger(NotificationService.class);

    @Autowired
    private NotificationRepository notificationRepository;

    @Autowired
    private CitoyenRepository citoyenRepository;

    @Autowired
    private FirebaseService firebaseService;

    /**
     * Récupère toutes les notifications du citoyen connecté
     */
    public List<NotificationResponse> obtenirNotificationsCitoyen() {
        Citoyen citoyen = getCitoyenConnecte();
        
        return notificationRepository.findByCitoyenIdOrderByDateEnvoiDesc(citoyen.getId()).stream()
                .map(this::convertirEnResponse)
                .collect(Collectors.toList());
    }

    /**
     * Récupère les notifications non lues du citoyen connecté
     */
    public List<NotificationResponse> obtenirNotificationsNonLues() {
        Citoyen citoyen = getCitoyenConnecte();
        
        return notificationRepository.findByCitoyenIdAndEstLueFalseOrderByDateEnvoiDesc(citoyen.getId()).stream()
                .map(this::convertirEnResponse)
                .collect(Collectors.toList());
    }

    /**
     * Compte les notifications non lues
     */
    public Long compterNotificationsNonLues() {
        Citoyen citoyen = getCitoyenConnecte();
        return notificationRepository.countByCitoyenIdAndEstLueFalse(citoyen.getId());
    }

    /**
     * Marque une notification comme lue
     */
    public NotificationResponse marquerCommeLue(Long notificationId) {
        Notification notification = notificationRepository.findById(notificationId)
                .orElseThrow(() -> new RuntimeException("Notification non trouvée"));
        
        notification.marquerLue();
        notificationRepository.save(notification);
        
        return convertirEnResponse(notification);
    }

    /**
     * Marque toutes les notifications comme lues
     */
    public void marquerToutesCommeLues() {
        Citoyen citoyen = getCitoyenConnecte();
        List<Notification> notifications = notificationRepository
                .findByCitoyenIdAndEstLueFalseOrderByDateEnvoiDesc(citoyen.getId());
        
        notifications.forEach(n -> n.marquerLue());
        notificationRepository.saveAll(notifications);
    }

    /**
     * Notifie tous les citoyens d'une mise à jour de procédure
     */
    public void notifierMiseAJourProcedure(Procedure procedure) {
        List<Citoyen> citoyens = citoyenRepository.findAllActifs();
        
        String contenu = String.format(
                "La procédure '%s' a été mise à jour. Nouveau coût: %d FCFA, Nouveau délai: %s",
                procedure.getTitre(),
                procedure.getCout(),
                procedure.getDelai()
        );
        
        for (Citoyen citoyen : citoyens) {
            // Créer la notification en base
            Notification notification = new Notification();
            notification.setContenu(contenu);
            notification.setType("MISE_A_JOUR");
            notification.setCitoyen(citoyen);
            notification.setProcedure(procedure);
            notificationRepository.save(notification);
            
            // Envoyer la notification push
            if (citoyen.getTokenFcm() != null) {
                firebaseService.envoyerNotificationPush(
                        citoyen.getTokenFcm(),
                        "Mise à jour de procédure",
                        contenu
                );
            }
        }
        
        logger.info("Notifications de mise à jour envoyées pour la procédure: {}", procedure.getNom());
    }

    /**
     * Envoie une notification à un citoyen spécifique
     */
    public void envoyerNotification(Long citoyenId, String contenu, String type, Procedure procedure) {
        Citoyen citoyen = citoyenRepository.findById(citoyenId)
                .orElseThrow(() -> new RuntimeException("Citoyen non trouvé"));
        
        Notification notification = new Notification();
        notification.setContenu(contenu);
        notification.setType(type);
        notification.setCitoyen(citoyen);
        notification.setProcedure(procedure);
        notificationRepository.save(notification);
        
        // Envoyer la notification push
        if (citoyen.getTokenFcm() != null) {
            firebaseService.envoyerNotificationPush(
                    citoyen.getTokenFcm(),
                    type,
                    contenu
            );
        }
        
        logger.info("Notification envoyée au citoyen: {} {}", citoyen.getNom(), citoyen.getPrenom());
    }

    /**
     * Récupère le citoyen connecté
     */
    private Citoyen getCitoyenConnecte() {
        Authentication authentication = SecurityContextHolder.getContext().getAuthentication();
        UserDetailsImpl userDetails = (UserDetailsImpl) authentication.getPrincipal();
        
        return citoyenRepository.findById(userDetails.getId())
                .orElseThrow(() -> new RuntimeException("Citoyen non trouvé"));
    }

    /**
     * Convertit une notification en NotificationResponse
     */
    private NotificationResponse convertirEnResponse(Notification notification) {
        NotificationResponse response = new NotificationResponse();
        response.setId(notification.getId());
        response.setContenu(notification.getContenu());
        response.setDateEnvoi(notification.getDateEnvoi());
        response.setEstLue(notification.getEstLue());
        response.setType(notification.getType());
        
        if (notification.getProcedure() != null) {
            response.setProcedureId(notification.getProcedure().getId());
            response.setProcedureNom(notification.getProcedure().getNom());
        }
        
        return response;
    }
}
