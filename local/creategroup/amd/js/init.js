let selCustomer=document.querySelector('#id_tecustomer');
let selgroup=document.querySelector('#id_tegrouplist');
let bosubmit=document.querySelector('#id_bosubmit');
bosubmit.classList.remove('btn-secondary');
bosubmit.classList.add('btn-primary');
let boremove=document.querySelector('#id_boremove');
boremove.classList.remove('btn-secondary');
boremove.classList.add('btn-danger');
let tegroup=document.querySelector('#id_tegroup');
let divAfterboAddNew=document.querySelector("#fitem_id_bosubmit>div:nth-child(1)");
let divAfterboAddNew2=document.querySelector("#fitem_id_bosubmit>div:nth-child(2)");
let divAfterboRemove=document.querySelector("#fitem_id_boremove>div:nth-child(1)");
let divAfterboRemove2=document.querySelector("#fitem_id_boremove>div:nth-child(2)");

let fititemsubmit=document.querySelector("#fitem_id_bosubmit");
let fititemremove=document.querySelector("#fitem_id_boremove");

fititemsubmit.classList.remove('form-group','row');
fititemremove.classList.remove('form-group','row');
divAfterboAddNew.remove();
divAfterboAddNew2.className='';
divAfterboRemove.remove();
divAfterboRemove2.className='';

let newlayerbutton=document.createElement('div');
newlayerbutton.classList.add('flex','row');
boremove.append(newlayerbutton);

bosubmit.addEventListener('click',(e)=>{
    if (tegroup.value!==''){
        let xhr = new XMLHttpRequest();

        xhr.onload=addElement;
        xhr.open('POST','index.php',true);
        const formData= new FormData();
        formData.append('group',tegroup.value);
        formData.append('idcustomer',selCustomer.value);
        formData.append('operation','save');
        //Envio de datos
        xhr.send(formData);
    } else {
        alert("You must write a group");
        tegroup.focus();
    }
    
    
});

boremove.addEventListener('click',(e)=>{
    if (selgroup.value===''){
        alert('You must select a group');
        return;
    }
        let xhr = new XMLHttpRequest();
        xhr.onload=removeElement;
        xhr.open('POST','index.php',true);
        const formData= new FormData();
        formData.append('group',selgroup.value);
        formData.append('idcustomer',selCustomer.value);
        formData.append('operation','remove');
        //Envio de datos
        xhr.send(formData);
    
});

function removeElement(){
    if (this.readyState=== 4 && this. status === 200){
        if (JSON.parse(this.response)){
            selgroup.remove(selgroup.selectedIndex);

        } 
    }
}

function addElement(){
        if (this.readyState=== 4 && this. status === 200){
            if (JSON.parse(this.response)){
                let option=document.createElement('option');
                option.text=JSON.parse(this.response).id+' - '+JSON.parse(this.response).name;
                option.value=JSON.parse(this.response).id;
                selgroup.add(option);
             
            } 
        }
}


selCustomer.addEventListener('change',(e)=>{
    const idCustomer=e.target.value;
    let xhr = new XMLHttpRequest();
    xhr.onload=reqHandlerAddElem;
    xhr.open('POST','index.php',true);
    const formData= new FormData();
    formData.append('idcustomer',idCustomer);
    
    //Envio de datos
    xhr.send(formData);
});

function reqHandlerAddElem(){
    if (this.readyState=== 4 && this. status === 200){
        if ((JSON.parse(this.response.length))>0){
            selgroup.innerHTML="";
            JSON.parse(this.response).map(element => {
                let option=document.createElement('option');
                option.text=element.id+ ' - ' +element.name;
                option.value=element.id;
                selgroup.add(option);
            });

        } else {
            alert("No customer, no group :/");
            selCustomer.focus();
        }
    }
}