package fr.amlezia.camions.repository;

import fr.amlezia.camions.model.CamionsModel;
import org.springframework.data.jpa.repository.JpaRepository;
import org.springframework.stereotype.Repository;

@Repository
public interface CamionRepository extends JpaRepository<CamionsModel, Long> {
}
