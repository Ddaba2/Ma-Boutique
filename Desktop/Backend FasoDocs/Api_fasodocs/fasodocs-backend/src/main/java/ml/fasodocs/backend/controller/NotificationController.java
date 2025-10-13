package ml.fasodocs.backend.controller;

import io.swagger.v3.oas.annotations.Operation;
import io.swagger.v3.oas.annotations.tags.Tag;
import ml.fasodocs.backend.dto.response.NotificationResponse;
import ml.fasodocs.backend.service.NotificationService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import java.util.List;

/**
 * Contrôleur REST pour la gestion des notifications
 */
@Tag(name = "Notifications", description = "API pour la gestion des notifications")
@RestController
@RequestMapping("/api/notifications")
@CrossOrigin(origins = "*", maxAge = 3600)
public class NotificationController {

    @Autowired
    private NotificationService notificationService;

    /**
     * Récupère toutes les notifications du citoyen connecté
     */
    @Operation(summary = "Récupère toutes les notifications du citoyen connecté")
    @GetMapping
    public ResponseEntity<List<NotificationResponse>> obtenirNotifications() {
        return ResponseEntity.ok(notificationService.obtenirNotificationsCitoyen());
    }

    /**
     * Récupère les notifications non lues
     */
    @Operation(summary = "Récupère les notifications non lues")
    @GetMapping("/non-lues")
    public ResponseEntity<List<NotificationResponse>> obtenirNotificationsNonLues() {
        return ResponseEntity.ok(notificationService.obtenirNotificationsNonLues());
    }

    /**
     * Compte les notifications non lues
     */
    @Operation(summary = "Compte les notifications non lues")
    @GetMapping("/count-non-lues")
    public ResponseEntity<Long> compterNotificationsNonLues() {
        return ResponseEntity.ok(notificationService.compterNotificationsNonLues());
    }

    /**
     * Marque une notification comme lue
     */
    @Operation(summary = "Marque une notification comme lue")
    @PutMapping("/{id}/lire")
    public ResponseEntity<NotificationResponse> marquerCommeLue(@PathVariable Long id) {
        return ResponseEntity.ok(notificationService.marquerCommeLue(id));
    }

    /**
     * Marque toutes les notifications comme lues
     */
    @Operation(summary = "Marque toutes les notifications comme lues")
    @PutMapping("/lire-tout")
    public ResponseEntity<Void> marquerToutesCommeLues() {
        notificationService.marquerToutesCommeLues();
        return ResponseEntity.ok().build();
    }
}
