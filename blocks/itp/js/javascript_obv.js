const bosubmit=document.querySelector('#fitem_id_bosubmit');

const veselSelect=document.querySelector('#id_selgroup');
bosubmit.classList.remove('row');

bosubmit.addEventListener('click',(ev)=>{
    const form= document.querySelector('#filterformid');
    
    form.submit();

});


