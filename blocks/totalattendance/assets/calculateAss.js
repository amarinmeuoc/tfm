
totalAtt=0;
window.addEventListener('load',()=>{
    let assessment_arr=document.querySelectorAll('.linkatt');
    let total=0;
    let cont=0;
    assessment_arr.forEach((link)=>{
        if (link.textContent!=='-'){
            total+=parseFloat(link.textContent);
            cont++;
        }
    })
    if (cont!==0)
        totalAtt=total/cont;
    //Adding progress circular progress bar;
    let barass = new ProgressBar.Circle('#containeratt', {
        color: '#aaa',
        // This has to be the same size as the maximum width to
        // prevent clipping
        strokeWidth: 4,
        trailWidth: 1,
        easing: 'easeInOut',
        duration: 1400,
        text: {
          autoStyleContainer: false
        },
        from: { color: '#a00', width: 1 },
        to: { color: '#00f', width: 4 },
        // Set default step function for all animate calls
        step: function(state, circle) {
          circle.path.setAttribute('stroke', state.color);
          circle.path.setAttribute('stroke-width', state.width);
      
          var value = circle.value() * 100;
          if (value === 0) {
            circle.setText('-');
          } else {
            circle.setText(value.toFixed(2));
          }
      
        }
      });
    
    barass.text.style.fontFamily = '"Raleway", Helvetica, sans-serif';
    barass.text.style.fontSize = '2rem';
    
    barass.animate(totalAtt/100);  // Number from 0.0 to 1.0
})



