const selcustomer=document.querySelector('#id_selcustomer');
if (selcustomer!==null){
  selcustomer.addEventListener('change',(e)=>{
    window.console.log(e.target.value);
    requestGroup(e.target.value);
  });
} 


const requestGroup=(customershortname)=>{
    const xhr=new XMLHttpRequest();
    const url=window.location.protocol+'//'+window.location.hostname+'/webservice/rest/server.php';
    xhr.open('POST',url,true);
    const token=document.querySelector('#id_token').value;
    const formData=new FormData();
    formData.append('wstoken',token);
    formData.append('wsfunction','report_partialplan_get_group_list');
    formData.append('moodlewsrestformat','json');
    formData.append('params[0][customershortname]',customershortname);
    
    xhr.send(formData);
    xhr.onload = (event) =>{
        onLoadFunction(xhr);
        };
      xhr.onprogress = (event)=>{
        onProgressFunction(event);
      } 
      xhr.onerror = function() {
        window.console.log("Solicitud fallida");
      };

}

const onLoadFunction=(myXhr)=>{
    if (myXhr.readyState=== 4 && myXhr. status === 200){
        const res=JSON.parse(myXhr.response);
        const group_list_selector=document.querySelector('#selgroupid');
        group_list_selector.innerHTML='';
        res.map((elem)=>{
            group_list_selector.innerHTML+='<option value="'+elem.id+'">'+elem.name+'</option>';
        })
    }
}

const onProgressFunction= (event)=>{
    if (event.lengthComputable) {
      window.console.log(`Recibidos ${event.loaded} de ${event.total} bytes`);
    } else {
      window.console.log(`Recibidos ${event.loaded} bytes`); // sin Content-Length
    }
}