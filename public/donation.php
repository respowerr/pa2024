<?include_once('maintenance_check.php');?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php
        $title = "Donation - ATD";
        include_once($_SERVER['DOCUMENT_ROOT'] . '/includes/head.php');
    ?>    
    <script src="/assets/js/translation.js"></script>
</head>
<body>
    <div class="wrapper">
        <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/includes/header.php') ?>
        <main>
            <div class="content">
                <h1 data-translate="donate_title">Faire un Don - Au Temps Donné</h1>
                
                <section>
                    <h2 data-translate="why_donate">Pourquoi Faire un Don ?</h2>
                    <p data-translate="donate_intro">Faire un don à Au Temps Donné, c'est contribuer directement à améliorer la vie de nombreuses personnes dans notre communauté. Vos dons nous permettent de fournir des repas nutritifs, d'offrir un soutien scolaire, et d'aider les personnes âgées et vulnérables à rester autonomes et connectées.</p>
                    <p data-translate="donate_impact">Chaque contribution, qu'elle soit petite ou grande, fait une différence significative. Grâce à votre générosité, nous pouvons continuer à offrir nos services essentiels et à étendre notre portée pour aider encore plus de personnes dans le besoin.</p>
                </section>

                <section>
                    <h2 data-translate="donation_impact">Impact de Votre Don</h2>
                    <ul>
                        <li data-translate="donate_10">10€ : Permettent de fournir un repas chaud à une famille de quatre personnes.</li>
                        <li data-translate="donate_20">20€ : Offrent du matériel scolaire et du soutien éducatif à un enfant pour un mois.</li>
                        <li data-translate="donate_50">50€ : Aident à financer une semaine de visites et d'accompagnement pour une personne âgée isolée.</li>
                        <li data-translate="donate_100">100€ : Contribuent à l'organisation d'un atelier communautaire pour sensibiliser et éduquer sur des sujets importants comme la nutrition et la santé.</li>
                    </ul>
                </section>

                <section>
                    <h2 data-translate="how_to_donate">Comment Faire un Don</h2>
                    <p data-translate="donate_options">Faire un don à Au Temps Donné est simple et sécurisé. Vous pouvez choisir parmi les options suivantes :</p>
                    <ul>
                        <li data-translate="donate_online">Faire un don en ligne via notre plateforme sécurisée. Cliquez sur le bouton ci-dessous pour commencer :</li>
                        <li data-translate="donate_check">Envoyer un chèque à l'adresse suivante : 123 Rue de la Solidarité, 75000 Paris, France.</li>
                        <li data-translate="donate_transfer">Faire un virement bancaire. Contactez-nous à <a href="mailto:donations@autempsdonne.fr">donations@autempsdonne.fr</a> pour obtenir nos coordonnées bancaires.</li>
                    </ul>
                    <button onclick="window.location.href='/donation_form.php'" data-translate="donate_button">Faire un Don en Ligne</button>
                </section>

                <section>
                    <h2 data-translate="donations_in_action">Vos Dons en Action</h2>
                    <p data-translate="support_thanks">Grâce à votre soutien, Au Temps Donné peut continuer à :</p>
                    <ul>
                        <li data-translate="action_food">Organiser des distributions alimentaires hebdomadaires pour les familles dans le besoin.</li>
                        <li data-translate="action_education">Fournir un soutien éducatif et des ressources aux enfants pour les aider à réussir à l'école.</li>
                        <li data-translate="action_support">Offrir des services d'accompagnement et de soutien aux personnes âgées et vulnérables.</li>
                        <li data-translate="action_community">Créer des programmes et des ateliers communautaires pour renforcer les liens sociaux et améliorer la qualité de vie.</li>
                    </ul>
                </section>

                <section>
                    <h2 data-translate="thank_you">Merci de Votre Soutien</h2>
                    <p data-translate="thank_you_note">Nous vous remercions chaleureusement pour votre générosité et votre engagement envers notre cause. Ensemble, nous pouvons faire une réelle différence et apporter de l'espoir et du soutien à ceux qui en ont le plus besoin.</p>
                </section>

            </div>
        </main>
        <?php include_once($_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php')?>
    </div>
</body>
</html>
