package ml.fasodocs.backend.service;

import com.google.auth.oauth2.GoogleCredentials;
import com.google.firebase.FirebaseApp;
import com.google.firebase.FirebaseOptions;
import com.google.firebase.messaging.FirebaseMessaging;
import com.google.firebase.messaging.Message;
import com.google.firebase.messaging.Notification;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.beans.factory.annotation.Value;
import org.springframework.core.io.ClassPathResource;
import org.springframework.stereotype.Service;

import jakarta.annotation.PostConstruct;
import java.io.IOException;

/**
 * Service pour l'envoi de notifications push via Firebase Cloud Messaging
 */
@Service
public class FirebaseService {

    private static final Logger logger = LoggerFactory.getLogger(FirebaseService.class);

    @Value("${firebase.config.path}")
    private String firebaseConfigPath;

    @PostConstruct
    public void initialize() {
        try {
            if (FirebaseApp.getApps().isEmpty()) {
                FirebaseOptions options = FirebaseOptions.builder()
                        .setCredentials(GoogleCredentials.fromStream(
                                new ClassPathResource("firebase-service-account.json").getInputStream()))
                        .build();

                FirebaseApp.initializeApp(options);
                logger.info("Firebase initialisé avec succès");
            }
        } catch (IOException e) {
            logger.error("Erreur lors de l'initialisation de Firebase: {}", e.getMessage());
            // Ne pas bloquer l'application si Firebase n'est pas configuré
        }
    }

    /**
     * Envoie une notification push à un appareil
     */
    public void envoyerNotificationPush(String tokenFcm, String titre, String contenu) {
        try {
            if (FirebaseApp.getApps().isEmpty()) {
                logger.warn("Firebase non initialisé. Notification non envoyée.");
                return;
            }

            Message message = Message.builder()
                    .setToken(tokenFcm)
                    .setNotification(Notification.builder()
                            .setTitle(titre)
                            .setBody(contenu)
                            .build())
                    .build();

            String response = FirebaseMessaging.getInstance().send(message);
            logger.info("Notification push envoyée avec succès: {}", response);
        } catch (Exception e) {
            logger.error("Erreur lors de l'envoi de la notification push: {}", e.getMessage());
        }
    }

    /**
     * Envoie une notification push à plusieurs appareils
     */
    public void envoyerNotificationPushMultiple(java.util.List<String> tokensFcm, String titre, String contenu) {
        for (String token : tokensFcm) {
            envoyerNotificationPush(token, titre, contenu);
        }
    }
}
