package ml.fasodocs.backend.entity;

import jakarta.persistence.*;
import lombok.AllArgsConstructor;
import lombok.Data;
import lombok.NoArgsConstructor;

/**
 * Entité représentant une loi ou un article de loi
 */
@Entity
@Table(name = "lois_articles")
@Data
@NoArgsConstructor
@AllArgsConstructor
public class LoiArticle {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;

    @Column(nullable = false, length = 500)
    private String description;

    @Column(name = "consulter_article", columnDefinition = "TEXT")
    private String consulterArticle;

    @Column(name = "lien_audio", length = 500)
    private String lienAudio; // URL vers l'audio de l'article en bambara

    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(name = "procedure_id")
    private Procedure procedure;
}
