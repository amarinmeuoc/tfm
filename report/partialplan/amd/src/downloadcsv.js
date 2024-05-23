export const init = (customerid) => {
    let trainee_list=document.querySelectorAll('.bodownload');
    trainee_list.forEach((node)=>{
        node.addEventListener('click',(e)=>{
            downloadcsv(e,customerid);
        });
    })
    
    window.console.log("show trainees... ");
}

const downloadcsv=(e,customerid)=>{
    e.stopPropagation(); //To avoid the event bubbles up further up the DOM tree, stopping it at the button element
    const element=e.target.closest('a');
    let list_of_trainees=element.getAttribute('data-id');
    list_of_trainees=list_of_trainees.split(",");
    list_of_trainees=list_of_trainees.reduce((arr,elem)=>{
        const aux= elem.split("_");
        return [...arr,{group:aux[0],billid:aux[1]}];
    },[]);
    //window.console.log(list_of_trainees);
    
    const xhr=new XMLHttpRequest();
    const url=window.location.protocol+'//'+window.location.hostname+'/webservice/rest/server.php';
    xhr.open('POST',url,true);
    const token=document.querySelector('#id_token').value;
    const formData= new FormData();
    formData.append('wstoken',token);
    formData.append('wsfunction','report_partialplan_get_trainees');
    formData.append('moodlewsrestformat','json');
    for (let i=0;i<list_of_trainees.length;i++){
        const group=list_of_trainees[i].group.trim();
        const billid=list_of_trainees[i].billid.trim();
        formData.append(`params[${i}][customerid]`,parseInt(customerid));
        formData.append(`params[${i}][group]`,group);
        formData.append(`params[${i}][billid]`,billid);
    }
            
    xhr.send(formData);
    xhr.onload = (event) =>{
        onLoadFunction(xhr,element);
        
        };
    xhr.onprogress = (event)=>{
        onProgressFunction(event);
    } 
    xhr.onerror = function() {
        window.console.log("Solicitud fallida");
    };
        
   
    
}

const onLoadFunction=(myXhr,element)=>{
    if (myXhr.readyState=== 4 && myXhr. status === 200){
      const res=JSON.parse(myXhr.response);
      
      //getting tittle keys
      const titleKeys=Object.keys(res[0]);
      const refinedData=[];

      refinedData.push(titleKeys);
      res.forEach(item=>{
        refinedData.push(Object.values(item));
      });

      let csvContent='';
      refinedData.forEach(row=>{
        csvContent+=row.join(';')+'\n';
      });

      const blob= new Blob([csvContent],{type:'text/csv;charset=utf-8,'});
      const objURL=URL.createObjectURL(blob);
      element.setAttribute('href',objURL);
      element.setAttribute('download','file.csv');
      
        
  }
  }
  
  const onProgressFunction= (event)=>{
      if (event.lengthComputable) {
        window.console.log(`Recibidos ${event.loaded} de ${event.total} bytes`);
      } else {
        window.console.log(`Recibidos ${event.loaded} bytes`); // sin Content-Length
      }
  }