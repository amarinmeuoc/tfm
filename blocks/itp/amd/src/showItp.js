import {exception as displayException} from 'core/notification';
import Templates from 'core/templates';
import { sortITPTable } from './commonFunctions';

export const init = (token,customer,group,billid,firstname,lastname) => {
    let linkass=document.querySelector('.back');
    linkass.addEventListener('click',(e)=>{
        linkAssFunctionEvent(e,token,customer,group,billid,firstname,lastname);
    });
}

const linkAssFunctionEvent=(e,token,customer,group,billid,firstname,lastname)=>{
    e.preventDefault();
    const xhr=new XMLHttpRequest();
    const url=window.location.protocol+'//'+window.location.hostname+'/webservice/rest/server.php';
    xhr.open('POST',url,true);
    const formData= new FormData();
    formData.append('wstoken',token);
    formData.append('wsfunction','block_itp_get_itp');
    formData.append('moodlewsrestformat','json');
    formData.append('params[0][orderby]','startdate');
    formData.append('params[0][order]','ASC');
    formData.append('params[0][customer]',customer);
    formData.append('params[0][group]',group);
    formData.append('params[0][billid]',billid);
    xhr.send(formData);
    xhr.onload=(event)=>{
        onLoadFunction(xhr,firstname,lastname,token);
    };
    xhr.onprogress=(event)=>{
        onProgressFunction(event);
    };
    xhr.onerror=()=>{
        onErrorFunction();
    };
}

const onProgressFunction = (event)=> {
    if (event.lengthComputable) {
      window.console.log(`Recibidos ${event.loaded} de ${event.total} bytes`);
    } else {
      window.console.log(`Recibidos ${event.loaded} bytes`); // sin Content-Length
    }
  };
  
const onErrorFunction = () => {
    window.console.log("Solicitud fallida");
  };

const onLoadFunction=(myXhr,firstname,lastname,token)=>{
    if (myXhr.readyState=== 4 && myXhr. status === 200){
        const res=JSON.parse(myXhr.response);
        const formattedData = {
            token:token,
            user:{
                firstname:firstname,
                lastname:lastname,
                email:(res.length>0)?res[0].email:'',
            },
          itp: res.map(item => {
            // Customize object properties for the template as needed
            return {
              // Example:
              id:item.id,
              coursename: item.coursename,
              duration: item.duration,
              startdate:item.startdate,
              shortcode:item.shortcode,
              courseId:item.courseid,
              groupid:item.groupid,
              customerid:item.customerid,
              billid:item.billid,
              email:item.email,
              enddate:item.enddate,
              location:item.location,
              classroom:item.classroom,
              schedule:item.schedule,
              attendance:item.attendance,
              assessment:item.assessment,
              lastupdate:item.lastupdate,
              courseUrl:item.courseUrl
              // ... add other properties
             
              
            };
          })
        }
        showTemplateItp(formattedData);
    }
}

const showTemplateItp=(response)=>{
    Templates.renderForPromise('block_itp/itp',response)
    .then(({html,js})=>{
        const content=document.querySelector('.block_itp_content');
        content.innerHTML="";
        Templates.appendNodeContents('.block_itp_content',html,js);
    })
    .then(()=>{
        sortITPTable();
    })
    .catch((error)=>displayException(error));
}

