<div class="_becomePremiumMember">

  <div class="modalback"></div>
  <div id="upgradeModal_closeBtn" class="close-btn">&times;</div>

  <div class="premiumModal">
    <div class="premiumModalImage">
      <img src="/images/upgrade_logo.png" />
    </div>

    <h1>Are you interested in GUARANTEED ADMISSION to a University?</h1>

    <h5>Become a Plexuss Premium member for:</h5>

    <div class="detailBlock">
      <div class="guaranteedImage">
        <img src="/images/guaranteed_image.png" />
      </div>
      <ul>
        <li>
          <img class="checkMark" src="/images/checkmark.png">
          1-on-1 Support
        </li>
        <li>
          <img class="checkMark" src="/images/checkmark.png">
          Free Applications
        </li>
        <li>
          <img class="checkMark" src="/images/checkmark.png">
          Guaranteed 1-20 to get your visa
        </li>
      </ul>

    </div>

    <?php
      $link = "/checkout/premium?cameFrom=" . $_SERVER['REQUEST_URI'];
    ?>
    <div class="row">
      <div class="large-6 medium-6 small-12 columns">
        <a href={{$link}} class="goto-upgrade-btn yes">Yes, I want to be accepted!</a>
      </div>
      <div class="large-6 medium-6 small-12 columns">
        <a href={{$link}} class="goto-upgrade-btn no">No, I am not ready yet</a>
      </div>
    </div>
  </div>


</div>
