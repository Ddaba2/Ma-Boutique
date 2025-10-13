package ml.fasodocs.backend;

import org.springframework.boot.SpringApplication;
import org.springframework.boot.autoconfigure.SpringBootApplication;
import org.springframework.context.annotation.Bean;
import org.springframework.scheduling.annotation.EnableAsync;
import org.springframework.scheduling.annotation.EnableScheduling;
import org.springframework.web.servlet.config.annotation.CorsRegistry;
import org.springframework.web.servlet.config.annotation.WebMvcConfigurer;

/**
 * Classe principale de l'application FasoDocs Backend
 * 
 * Application pour simplifier les démarches administratives au Mali
 * 
 * @author Tenen Madyeh SYLLA, Daba DIALLO
 * @version 1.0
 * @since 2025-10-01
 */
@SpringBootApplication
@EnableAsync
@EnableScheduling
public class FasodocsBackendApplication {

    public static void main(String[] args) {
        SpringApplication.run(FasodocsBackendApplication.class, args);
        System.out.println("\n========================================");
        System.out.println("   FasoDocs Backend démarré avec succès!");
        System.out.println("   API Documentation: http://localhost:8080/api/swagger-ui.html");
        System.out.println("========================================\n");
    }

    @Bean
    public WebMvcConfigurer corsConfigurer() {
        return new WebMvcConfigurer() {
            @Override
            public void addCorsMappings(CorsRegistry registry) {
                registry.addMapping("/**")
                        .allowedOrigins("*")
                        .allowedMethods("GET", "POST", "PUT", "DELETE", "OPTIONS")
                        .allowedHeaders("*")
                        .maxAge(3600);
            }
        };
    }
}
