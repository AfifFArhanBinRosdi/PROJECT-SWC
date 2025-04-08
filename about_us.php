<?php
session_start();

$page_title = "About Us";
$site_name = "HoyoVerse Merch";
require_once 'includes/header.php';

?>

<div class="container my-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <h1 class="display-4 text-center mb-4">About HoyoVerse Merch</h1>
            <p class="lead text-center mb-5">
                Your premier destination for official merchandise from HoYoverse's beloved game universes.
            </p>
            
            <div class="card mb-4">
                <div class="card-body">
                    <h2 class="card-title">Our Story</h2>
                    <p class="card-text">
                        Founded in 2023, HoyoVerse Merch is dedicated to bringing authentic, high-quality merchandise 
                        from your favorite HoYoverse games directly to your doorstep. We partner directly with 
                        official distributors to ensure all products are 100% genuine.
                    </p>
                    <p class="card-text">
                        Our passionate team consists of gamers just like you, committed to providing excellent 
                        customer service and a seamless shopping experience.
                    </p>
                </div>
            </div>

            <h2 class="text-center my-5">Featured Games</h2>
            
            <!-- Genshin Impact Section -->
            <div class="card mb-4">
               <div class="card-img-top ratio ratio-16x9">
                    <iframe src="https://www.youtube.com/embed/vgdhWQ5aL0s" allowfullscreen></iframe>
                </div>
                <div class="card-body">
                    <h3 class="card-title">Genshin Impact</h3>
                    <p class="card-text">
                        Genshin Impact is an open-world action RPG that takes place in the vast world of Teyvat. 
                        You play as the Traveler, searching for your lost sibling across seven nations, each 
                        with unique cultures, stories, and elemental powers.
                    </p>
                    <p class="card-text">
                        With its gacha system, real-time combat, and breathtaking visuals, Genshin Impact has 
                        captivated millions of players worldwide since its release in 2020.
                    </p>
                    <a href="https://genshin.hoyoverse.com/" target="_blank" class="btn btn-primary">Official Website</a>
                </div>
            </div>
            
            <!-- Honkai: Star Rail Section -->
            <div class="card mb-4">
                <div class="card-img-top ratio ratio-16x9">
                    <iframe src="https://www.youtube.com/embed/dywDrslYCJg" allowfullscreen></iframe>
                </div>
                <div class="card-body">
                    <h3 class="card-title">Honkai: Star Rail</h3>
                    <p class="card-text">
                        Honkai: Star Rail is a space fantasy RPG that takes players on a cosmic adventure across 
                        the stars. Board the Astral Express and travel to diverse worlds, each with unique 
                        civilizations, stories, and challenges.
                    </p>
                    <p class="card-text">
                        Featuring turn-based combat, strategic team-building, and a rich sci-fi narrative, 
                        Star Rail expands the Honkai universe with new characters and interstellar mysteries.
                    </p>
                    <a href="https://hsr.hoyoverse.com/" target="_blank" class="btn btn-primary">Official Website</a>
                </div>
            </div>
            
            <!-- Zenless Zone Zero Section -->
            <div class="card mb-4">
                <div class="card-img-top ratio ratio-16x9">
                    <iframe src="https://www.youtube.com/embed/q-Y0AqzOxQI" allowfullscreen></iframe>
                </div>
                <div class="card-body">
                    <h3 class="card-title">Zenless Zone Zero</h3>
                    <p class="card-text">
                        Zenless Zone Zero (ZZZ) is an urban fantasy action RPG set in a post-apocalyptic metropolis 
                        where reality and alternate dimensions collide. As a Proxy, you guide clients through 
                        dangerous anomalies called Hollows.
                    </p>
                    <p class="card-text">
                        With its stylish urban aesthetic, fast-paced combat, and roguelike elements, ZZZ offers 
                        a fresh take on the action RPG genre from HoYoverse.
                    </p>
                    <a href="https://zenless.hoyoverse.com/en-us/main" target="_blank" class="btn btn-primary">Official Website</a>
                </div>
            </div>
            
            <div class="card mt-5">
                <div class="card-body text-center">
                    <h3 class="card-title">Our Commitment</h3>
                    <p class="card-text">
                        At HoyoVerse Merch, we're committed to bringing you the highest quality official merchandise 
                        while providing exceptional customer service. Have questions? Contact our support team anytime!
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>