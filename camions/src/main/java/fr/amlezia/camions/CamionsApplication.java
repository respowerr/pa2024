package fr.amlezia.camions;

import org.springframework.boot.SpringApplication;
import org.springframework.boot.autoconfigure.SpringBootApplication;

@SpringBootApplication
public class CamionsApplication {

	public static void main(String[] args) {
		SpringApplication.run(CamionsApplication.class, args);
		System.out.println("[ HELIX ] Welcome to truck API of HELIX");
	}

}
