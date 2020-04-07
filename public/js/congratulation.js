$(window).on("load resize",function(e){
       var myNode = document.getElementById("confetti-wrapper");
       while (myNode.firstChild) { 
           myNode.removeChild(myNode.firstChild);
       }
       confetti();
});
function confetti(){
       for(i=0; i<250; i++) {
        // Random rotation
              var randomRotation = Math.floor(Math.random() * 360);
              // Random width & height between 0 and viewport
              if($(window).width() < 768){
                     var randomWidth = Math.floor(Math.random() * $(window).width() - 350);
                     if($(window).height() > 800){
                            var randomHeight =  Math.floor(Math.random() * $(window).height() - 790);
                     }else if($(window).height() > 730){
                            var randomHeight =  Math.floor(Math.random() * $(window).height() - 650);
                     }else if($(window).height() > 660){
                            var randomHeight =  Math.floor(Math.random() * $(window).height() - 500);
                     }else if($(window).height() > 630){
                            var randomHeight =  Math.floor(Math.random() * $(window).height() - 470);
                     }else if($(window).height() > 560){
                            var randomHeight =  Math.floor(Math.random() * $(window).height() - 250);
                     }else{
                            var randomHeight =  Math.floor(Math.random() * $(window).height() - 200);
                     }
              }else if($(window).width() < 1301){
                     var randomWidth = Math.floor(Math.random() * $(window).width() - 875);
                     if($(window).height() < 1081){
                            var randomHeight =  Math.floor(Math.random() * $(window).height() - 1050);
                     }else{
                            var randomHeight =  Math.floor(Math.random() * $(window).height() - 775);                            
                     }
              }else{
                     var randomWidth = Math.floor(Math.random() * $(window).width() - 880);
                     if($(window).height() < 1081){
                            var randomHeight =  Math.floor(Math.random() * $(window).height() - 1050);
                     }else{
                            var randomHeight =  Math.floor(Math.random() * $(window).height() - 770);                            
                     }
              }
              // Random animation-delay
              var randomAnimationDelay = Math.floor(Math.random() * 25);

              // Random colors
              var colors = ['#0CD977', '#FF1C1C', '#FF93DE', '#5767ED', '#FFC61C', '#8497B0']
              var randomColor = colors[Math.floor(Math.random() * colors.length)];

              // Create confetti piece
              var confetti = document.createElement('div');

              confetti.className = 'confetti';
              confetti.style.top=randomHeight + 'px';
              confetti.style.left=randomWidth + 'px';
              confetti.style.backgroundColor=randomColor;
              confetti.style.transform='skew(35deg) rotate(' + randomRotation + 'deg)';
              confetti.style.animationDelay=randomAnimationDelay + 's';
              document.getElementById("confetti-wrapper").appendChild(confetti);
       }
}
