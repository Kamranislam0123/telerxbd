<?php
/**
 * TeleRx Bangladesh — Medical Camp homepage short section
 *
 * Replace these three image files with your own photographs:
 * assets/img/medical-camp/home-camp-01.jpg
 * assets/img/medical-camp/home-camp-02.jpg
 * assets/img/medical-camp/home-camp-03.jpg
 */

$medicalCampShortImages = [
    [
        'src' => 'assets/img/medical-camp/Camp (3).jpeg',
        'alt' => 'TeleRx medical camp patient registration',
        'caption' => 'Patient registration',
        'icon' => 'fa-clipboard-user',
    ],
    [
        'src' => 'assets/img/medical-camp/Camp (1).jpeg',
        'alt' => 'Online doctor consultation at a TeleRx medical camp',
        'caption' => 'Online consultation',
        'icon' => 'fa-video',
    ],
    [
        'src' => 'assets/img/medical-camp/Camp (7).jpeg',
        'alt' => 'Basic health assessment at a TeleRx medical camp',
        'caption' => 'Basic health assessment',
        'icon' => 'fa-heart-pulse',
    ],
];
?>

<section class="trxmc-short" aria-labelledby="trxmc-short-title">
    <div class="container">
        <div class="trxmc-short-panel">
            <div class="trxmc-short-copy">
                <span class="trxmc-short-eyebrow">
                    <i class="fa-solid fa-house-medical-circle-check" aria-hidden="true"></i>
                    Community healthcare initiative
                </span>

                <h2 id="trxmc-short-title">Medical care brought closer through organized TeleRx camps.</h2>

                <p>
                    TeleRx Bangladesh arranges medical camps in villages, schools, educational
                    institutions, orphanages and community locations. Patients receive supported
                    online consultation with doctors through the TeleRx website.
                </p>

                <div class="trxmc-short-points" aria-label="Medical camp highlights">
                    <div>
                        <i class="fa-solid fa-location-dot" aria-hidden="true"></i>
                        <span><strong>Local access</strong> Camps organized inside the community.</span>
                    </div>
                    <div>
                        <i class="fa-solid fa-user-doctor" aria-hidden="true"></i>
                        <span><strong>Doctor consultation</strong> Guided online medical care.</span>
                    </div>
                    <div>
                        <i class="fa-solid fa-file-prescription" aria-hidden="true"></i>
                        <span><strong>Clear next steps</strong> Advice, prescription or referral guidance.</span>
                    </div>
                </div>

                <a class="trxmc-short-button" href="medical-camp">
                    Know more about Medical Camp
                    <i class="fa-solid fa-arrow-right-long" aria-hidden="true"></i>
                </a>
            </div>

            <div class="trxmc-short-gallery" aria-label="TeleRx medical camp photographs">
                <?php foreach ($medicalCampShortImages as $index => $image): ?>
                    <?php
                    $absoluteImagePath = __DIR__ . '/' . $image['src'];
                    $itemClass = 'trxmc-short-photo trxmc-short-photo-' . ($index + 1);
                    ?>
                    <figure class="<?php echo htmlspecialchars($itemClass, ENT_QUOTES, 'UTF-8'); ?>">
                        <?php if (is_file($absoluteImagePath)): ?>
                            <img
                                src="<?php echo htmlspecialchars($image['src'], ENT_QUOTES, 'UTF-8'); ?>"
                                alt="<?php echo htmlspecialchars($image['alt'], ENT_QUOTES, 'UTF-8'); ?>"
                                loading="lazy"
                            >
                        <?php else: ?>
                            <div class="trxmc-short-placeholder" aria-label="<?php echo htmlspecialchars($image['alt'], ENT_QUOTES, 'UTF-8'); ?>">
                                <i class="fa-solid <?php echo htmlspecialchars($image['icon'], ENT_QUOTES, 'UTF-8'); ?>" aria-hidden="true"></i>
                                <span>Replace with camp photo <?php echo $index + 1; ?></span>
                            </div>
                        <?php endif; ?>

                        <figcaption>
                            <i class="fa-solid <?php echo htmlspecialchars($image['icon'], ENT_QUOTES, 'UTF-8'); ?>" aria-hidden="true"></i>
                            <?php echo htmlspecialchars($image['caption'], ENT_QUOTES, 'UTF-8'); ?>
                        </figcaption>
                    </figure>
                <?php endforeach; ?>

                <div class="trxmc-short-floating-note">
                    <i class="fa-solid fa-wifi" aria-hidden="true"></i>
                    <span>
                        <strong>Connected care</strong>
                        Telemedicine-enabled medical camp
                    </span>
                </div>
            </div>
        </div>
    </div>
</section>
