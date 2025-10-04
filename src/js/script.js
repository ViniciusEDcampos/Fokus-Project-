const btnSecret = document.getElementById('btn-secret');
let clicks = 0;

function secretSite(){
    if(clicks === 3){
        window.location.href = 'maxFokus.php';
    }
}

btnSecret.onclick = () => {
  clicks++;
  secretSite();
};

