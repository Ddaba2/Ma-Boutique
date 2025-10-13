package ml.fasodocs.backend.repository;

import ml.fasodocs.backend.entity.Chatbot;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

import java.util.Optional;

/**
 * Repository pour l'entité Chatbot
 */
@Repository
public interface ChatbotRepository extends JpaRepository<Chatbot, Long> {

    /**
     * Recherche un chatbot par type
     */
    Optional<Chatbot> findByType(String type);
}
