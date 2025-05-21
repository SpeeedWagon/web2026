<?php
// Assuming $pageTitle is set to something like "Acasă" or "SoluțiiInfo - Acasă"
// For the hero section, we'll have a more prominent title
?>

<!-- Hero Section -->
<div class="hero-section text-center"> <!-- Consider adding min-vh-75 or min-vh-100 and d-flex align-items-center justify-content-center if you want full viewport height with centered content -->
    <div class="container">
        <!-- You can add pt-5 or mt-5 directly to this container or the h1 to push content down if not using the CSS min-height/flex approach -->
        <h1 class="display-4 fw-bold mt-md-5">Soluții Inovatoare de Stocare a Informațiilor</h1>
        <p class="lead my-4">
            Protejăm activele digitale ale afacerii dumneavoastră cu tehnologii de stocare sigure, scalabile și accesibile.
        </p>
        <div class="d-flex justify-content-center align-items-center flex-wrap">
            <a href="index.php?page=about" class="btn btn-primary btn-lg m-2">Aflați Mai Multe Despre Noi</a>
            
            <!-- Social Media Icons -->
            <div class="social-icons m-2">
                <a href="https://twitter.com/yourcompany"><img src="https://upload.wikimedia.org/wikipedia/commons/c/ce/X_logo_2023.svg" width="32" height="32"/></a>
                <a href="https://instagram.com/yourcompany">
                <img src="https://upload.wikimedia.org/wikipedia/commons/e/e7/Instagram_logo_2016.svg" width="32" height="32"/>    
                <!-- <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-instagram" viewBox="0 0 16 16">
                        <path d="M8 0C5.829 0 5.556.01 4.703.048 3.85.088 3.269.222 2.76.42a3.9 3.9 0 0 0-1.417.923A3.9 3.9 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.703.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297.04.852.174 1.433.372 1.942.205.526.478.972.923 1.417.444.445.89.719 1.416.923.51.198 1.09.333 1.942.372C5.555 15.99 5.827 16 8 16s2.444-.01 3.298-.048c.851-.04 1.434-.174 1.943-.372.527-.205.973-.478 1.417-.923.445-.445.718-.891.923-1.417.197-.509.332-1.09.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.299c-.04-.851-.175-1.433-.372-1.941a3.9 3.9 0 0 0-.923-1.417A3.9 3.9 0 0 0 13.24.42c-.51-.198-1.092-.333-1.943-.372C10.443.01 10.172 0 7.998 0zm-.717 1.442h.718c2.136 0 2.389.007 3.232.046.78.035 1.204.166 1.486.275.373.145.64.319.92.599s.453.546.598.92c.11.281.24.705.275 1.485.039.843.047 1.096.047 3.231s-.008 2.389-.047 3.232c-.035.78-.166 1.203-.275 1.485a2.5 2.5 0 0 1-.599.919c-.28.28-.546.453-.92.598-.28.11-.704.24-1.485.276-.843.038-1.096.047-3.232.047s-2.39-.009-3.233-.047c-.78-.036-1.203-.166-1.485-.276a2.5 2.5 0 0 1-.92-.598 2.5 2.5 0 0 1-.598-.919c-.11-.281-.24-.705-.276-1.486-.038-.843-.046-1.095-.046-3.231s.008-2.389.046-3.232c.036-.78.166-1.204.276-1.486.145-.373.319-.64.599-.92s.546-.453.92-.598c.282-.11.705-.24 1.485-.276.738-.034 1.024-.044 2.515-.045zm4.988 1.328a.96.96 0 1 0 0 1.92.96.96 0 0 0 0-1.92m-4.27 1.122a4.109 4.109 0 1 0 0 8.217 4.109 4.109 0 0 0 0-8.217m0 1.441a2.667 2.667 0 1 1 0 5.334 2.667 2.667 0 0 1 0-5.334"/>
                    </svg> -->
                </a>
                <!-- Add more social icons here if needed (e.g., LinkedIn, Facebook) -->
            </div>
        </div>
    </div>
</div>

<!-- The rest of your page content (content-section, etc.) remains the same -->
<div class="container my-5">



    <!-- Additional Content Sections -->
    <div class="content-section p-4 p-md-5">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h3 class="text-primary">De Ce Să Alegeți Soluțiile Noastre?</h3>
                <p class="lead">Într-o lume digitală în continuă expansiune, securitatea și accesibilitatea datelor sunt esențiale. La SoluțiiInfo, înțelegem aceste nevoi și oferim:</p>
                <ul>
                    <li><strong>Securitate Avansată:</strong> Protecție robustă împotriva amenințărilor cibernetice și a pierderilor de date.</li>
                    <li><strong>Scalabilitate Flexibilă:</strong> Soluții care cresc odată cu afacerea dumneavoastră, fără costuri ascunse.</li>
                    <li><strong>Accesibilitate Ușoară:</strong> Accesați-vă informațiile oricând, de oriunde, în siguranță.</li>
                    <li><strong>Suport Dedicat:</strong> Echipa noastră de experți este gata să vă asiste 24/7.</li>
                </ul>
            </div>
            <div class="col-md-6 text-center">
                <img src="https://images.pexels.com/photos/3184465/pexels-photo-3184465.jpeg?auto=compress&cs=tinysrgb&w=600" class="img-fluid rounded shadow" alt="Echipă discutând soluții">
                <?php // Replace with a relevant image, perhaps an abstract data/server image ?>
            </div>
        </div>
    </div>

    <div class="content-section p-4 p-md-5">
        <h3 class="text-center text-primary mb-4">Serviciile Noastre Principale</h3>
        <div class="row">
            <div class="col-md-4 mb-3">
                <div class="card h-100 shadow-sm">
                    <div class="card-body text-center">
                         <img src="../media/svgs/cloud-server.svg" alt="Cloud Storage Icon" width="64" height="64" class="mb-3"> <!-- Placeholder SVG -->
                        <h5 class="card-title">Stocare Cloud Securizată</h5>
                        <p class="card-text">Păstrați-vă datele în siguranță în cloud-ul nostru criptat, cu backup-uri automate și redundanță.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card h-100 shadow-sm">
                    <div class="card-body text-center">
                        <img src="../media/svgs/shield-lock.svg" alt="Data Backup Icon" width="64" height="64" class="mb-3"> <!-- Placeholder SVG -->
                        <h5 class="card-title">Backup și Recuperare Date</h5>
                        <p class="card-text">Soluții complete de backup pentru a preveni pierderea datelor și a asigura continuitatea afacerii.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card h-100 shadow-sm">
                     <div class="card-body text-center">
                        <img src="../media/svgs/data-management.svg" alt="Data Management Icon" width="64" height="64" class="mb-3"> <!-- Placeholder SVG -->
                        <h5 class="card-title">Management Inteligent al Datelor</h5>
                        <p class="card-text">Organizați și gestionați eficient volume mari de date cu instrumentele noastre intuitive.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content-section p-4 p-md-5 text-center">
        <h3 class="text-primary">Sunteți Gata să Vă Transformați Gestionarea Datelor?</h3>
        <p class="lead">Contactați-ne astăzi pentru o consultanță gratuită și descoperiți cum SoluțiiInfo vă poate ajuta afacerea.</p>
        <a href="index.php?page=contact" class="btn btn-primary btn-lg mt-3">Cere o Ofertă Personalizată</a>
    </div>

</div>