const bosubmit=document.querySelector('#fitem_id_bosubmit');
const selCustomer=document.querySelector('#id_selCustomer');
const veselSelect=document.querySelector('#id_selgroup');
bosubmit.classList.remove('row');

bosubmit.addEventListener('click',(ev)=>{
    const form= document.querySelector('#filterformid');
    const formData= new FormData();
    const formSent=true;
    formData.append('formSent',formSent);
    form.submit();

});


selCustomer.addEventListener('change',(ev)=>{
        let xhr = new XMLHttpRequest();
        xhr.onload=reloadGroup;
        xhr.open('POST','.',true);
        const formData= new FormData();
        const selectedOption=ev.target.options[ev.target.selectedIndex].value;
        formData.append('selCustomer',selectedOption);

        //Envio de datos
        xhr.send(formData);
    
});

function reloadGroup(){
    if (this.readyState=== 4 && this. status === 200){
        if (this.response){
            const list_vesel=JSON.parse(this.response);
            
            removeOptions(veselSelect);
    
            for (const property in list_vesel){
                const opt=document.createElement('option');
                opt.value=property;
                opt.textContent=list_vesel[property];
                veselSelect.appendChild(opt);
            }
        } 
    }
}

function removeOptions(selectElement) {
    var i, L = selectElement.options.length - 1;
    for(i = L; i >= 0; i--) {
       selectElement.remove(i);
    }
 }