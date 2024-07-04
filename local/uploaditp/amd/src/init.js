import ModalDeleteCancel from 'core/modal_delete_cancel';
import ModalEvents from 'core/modal_events';
import notification from  'core/notification';

export const init = (token)=> {
    
    const boreset=document.querySelector('#id_removetables');
    boreset.classList.remove(['btn-secondary']);
    boreset.classList.add(['btn-danger']);
    boreset.addEventListener('click',()=>{
        popup(token);
        
    })
}

export const popup = async (token) => {
    const modal = await ModalDeleteCancel.create({
        title: 'Reset tables itp and partialplan',
        body: '<p>By doing this you confirm you want to remove all data in the tables: <strong>mdl_itp</strong> and <strong>mdl_partialplan</strong>.</p><p>Are you sure?</p>',
        
        
    });
    modal.getRoot().on(ModalEvents.delete, (e)=>{
        //Being here the user confirms to reset the tables itp and partialplan
        requestTableReset(token);
    })
    modal.show();
}

const requestTableReset=(token)=>{
    const xhr=new XMLHttpRequest();
    const url=window.location.protocol+'//'+window.location.hostname+'/webservice/rest/server.php';
    xhr.open('POST',url,true);
    const formData=new FormData();
    formData.append('wstoken',token);
    formData.append('wsfunction','local_uploaditp_remove_tables');
    formData.append('moodlewsrestformat','json');
    formData.append('params[0][op]','yes');
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

const onProgressFunction= (event)=>{
    if (event.lengthComputable) {
      window.console.log(`Recibidos ${event.loaded} de ${event.total} bytes`);
    } else {
      window.console.log(`Recibidos ${event.loaded} bytes`); // sin Content-Length
    }
}

const onLoadFunction=(myXhr)=>{
    if (myXhr.readyState===4 && myXhr.status===200){
        const res=JSON.parse(myXhr.response);
        if (res[0].result===true){
            notification.addNotification({
                message: "Tables have been successfully reset",
                type: "info"
            })
        }
        
        
    }
}





