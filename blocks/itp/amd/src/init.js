export const init=(token)=>{
    const bosubmit=document.querySelector('#fitem_id_bosubmit');
    const selCustomer=document.querySelector('#id_selCustomer');
    const groupSelect=document.querySelector('#id_selgroup');
    const selListTrainees=document.querySelector('#id_list_trainees');

    bosubmit.classList.remove('row');

        updateListTrainees(selCustomer,groupSelect,selListTrainees,token);
    
groupSelect.addEventListener('change',(ev)=>{
    updateListTrainees(selCustomer,groupSelect,selListTrainees,token);
},token)

selCustomer.addEventListener('change',(ev)=>{
    let xhr = new XMLHttpRequest();
    const url=window.location.protocol+'//'+window.location.hostname+'/webservice/rest/server.php';
    
    xhr.open('POST',url,true);
    const formData= new FormData();
    formData.append('wstoken',token);
    formData.append('wsfunction','block_itp_get_group');
    formData.append('moodlewsrestformat','json');
    formData.append('params[0][customercode]',ev.target.options[ev.target.selectedIndex].value);
    

    xhr.send(formData);
    xhr.onload = reloadGroup;
    
    xhr.onerror = function() {
        window.console.log("Solicitud fallida");
    };
},token);



function reloadGroup(){
    if (this.readyState=== 4 && this. status === 200){
        if (this.response){
            const list_group=JSON.parse(this.response);
            
            removeOptions(groupSelect);

            list_group.map(obj=>{
                const opt=document.createElement('option');
                opt.value=obj.id;
                opt.textContent=obj.name;
                groupSelect.appendChild(opt);
            });
            updateListTrainees(selCustomer,groupSelect,selListTrainees,token)
        } 
    }
}


}

const updateListTrainees=(selCustomer,groupSelect,listTrainees,token)=>{
    let xhr = new XMLHttpRequest();
    const url=window.location.protocol+'//'+window.location.hostname+'/webservice/rest/server.php';
    const selectedCustomer=selCustomer.options[selCustomer.selectedIndex].value;
    const selectedGroup=groupSelect.options[groupSelect.selectedIndex].textContent;
    xhr.open('POST',url,true);
    const formData= new FormData();
    formData.append('wstoken',token);
    formData.append('wsfunction','block_itp_get_trainee_list');
    formData.append('moodlewsrestformat','json');
    formData.append('params[0][customercode]',selectedCustomer);
    formData.append('params[0][groupname]',selectedGroup);
    xhr.send(formData);
    xhr.onload = ()=>{
        reloadTraineeList(xhr,listTrainees)
    };
    
    xhr.onerror = function() {
        window.console.log("Solicitud fallida");
    };
}


const reloadTraineeList=(xhr,listTrainees)=>{
    if (xhr.readyState=== 4 && xhr. status === 200){
        if (xhr.response){
            const list_trainees=JSON.parse(xhr.response);
            
            removeOptions(listTrainees);

            list_trainees.map(obj=>{
                const opt=document.createElement('option');
                opt.value=obj.billid;
                opt.textContent=obj.groupname+"_"+obj.billid+" "+obj.firstname+", "+obj.lastname;
                listTrainees.appendChild(opt);
            })

            let activeSpan = document.querySelector('#fitem_id_list_trainees>div>div>span[role="option"]');
            if (activeSpan!==null){
                activeSpan.setAttribute('data-value',(list_trainees.length===0)?'':list_trainees[0].billid);
                const span=document.createElement('span');
                span.setAttribute('aria-hidden',true);
                span.textContent="× "
                activeSpan.innerHTML="";
                activeSpan.appendChild(span);
                activeSpan.innerHTML+= list_trainees[0].groupname+"_"+list_trainees[0].billid+" "+list_trainees[0].firstname+", "+list_trainees[0].lastname;
            }
            

            
        } 
    }
}

const removeOptions=(selectElement) =>{
    var i, L = selectElement.options.length - 1;
    for(i = L; i >= 0; i--) {
       selectElement.remove(i);
    }
 }