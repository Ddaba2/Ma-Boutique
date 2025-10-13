package ml.fasodocs.backend.service;

import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.beans.factory.annotation.Value;
import org.springframework.mail.SimpleMailMessage;
import org.springframework.mail.javamail.JavaMailSender;
import org.springframework.stereotype.Service;

/**
 * Service pour l'envoi d'emails
 */
@Service
public class EmailService {

    private static final Logger logger = LoggerFactory.getLogger(EmailService.class);

    @Autowired
    private JavaMailSender mailSender;

    @Value("${spring.mail.username}")
    private String fromEmail;

    /**
     * Envoie un email de vérification
     */
    public void envoyerEmailVerification(String toEmail, String codeVerification) {
        try {
            SimpleMailMessage message = new SimpleMailMessage();
            message.setFrom(fromEmail);
            message.setTo(toEmail);
            message.setSubject("FasoDocs - Vérification de votre compte");
            message.setText("Bienvenue sur FasoDocs!\n\n" +
                    "Pour vérifier votre compte, veuillez cliquer sur le lien suivant:\n" +
                    "http://localhost:8080/api/auth/verify?code=" + codeVerification + "\n\n" +
                    "Si vous n'avez pas créé de compte, veuillez ignorer cet email.\n\n" +
                    "Cordialement,\n" +
                    "L'équipe FasoDocs");

            mailSender.send(message);
            logger.info("Email de vérification envoyé à: {}", toEmail);
        } catch (Exception e) {
            logger.error("Erreur lors de l'envoi de l'email de vérification: {}", e.getMessage());
        }
    }

    /**
     * Envoie une notification par email
     */
    public void envoyerNotificationEmail(String toEmail, String sujet, String contenu) {
        try {
            SimpleMailMessage message = new SimpleMailMessage();
            message.setFrom(fromEmail);
            message.setTo(toEmail);
            message.setSubject("FasoDocs - " + sujet);
            message.setText(contenu);

            mailSender.send(message);
            logger.info("Notification email envoyée à: {}", toEmail);
        } catch (Exception e) {
            logger.error("Erreur lors de l'envoi de la notification email: {}", e.getMessage());
        }
    }
}
