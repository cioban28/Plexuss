<div class="_upgradePremiumModal ">

  <div class="modalback"></div>
  <div id="upgradeModal_closeBtn" class="close-btn">&times;</div>

  <div class="premiumModal">
    <div class="premiumModalImage">
      <img src="/images/upgrade_logo.png" />
    </div>

    <div class="guaranteedImage">
      <img src="/images/guaranteed_image.png" />
    </div>

    <h1>Plexuss Premium</h1>

    <h5>What's included</h5>

    <div class="detailBlock">
      <ul>
        <li>
          <img src="/images/checkmark.png">
          Guaranteed 1-20 Form within 12 Months
        </li>
        <li>
          <img src="/images/checkmark.png">
          One-on-one University Admission Assistance
        </li>
        <li>
          <img src="/images/checkmark.png">
          Access to Admission Essays written by Students Admitted to Elite Universities
        </li>
        <li>
          <img src="/images/checkmark.png">
          Expert Support Helping You Choose a School in the USA, UK, or Australia
        </li>
        <li>
          <img src="/images/checkmark.png">
          Five Waived College Application Fees to Select Universities
        </li>
      </ul>

    </div>

    <?php
      $link = "/checkout/premium?cameFrom=" . $_SERVER['REQUEST_URI'];
    ?>

    <a href={{$link}} class="goto-upgrade-btn">Get Plexuss Premium Today!</a>
  </div>


</div>
