import React,{useContext,Fragment} from 'react';
import {useState} from 'react';
import Context from '../store/store';
import {Radio,RadioGroup, FormControlLabel, FormControl, FormLabel, TextField, Button } from '@material-ui/core';
import { makeStyles } from '@material-ui/core/styles';
import axios from 'axios';
import qs from 'qs';
import { __ } from '@wordpress/i18n';
const useStyles = makeStyles({
      margin: {
        marginBottom: '20px',
      },
      color: {
        color: 'red',
    },
    colorOne:{
        color: 'green',
    },
});

export default function FinalStep(props) {
    const classes = useStyles();
    const[error,setError] = useState('');
    const[success,setSuccess] = useState('');
   
    const buttonclickhandler = (e) => {
        e.preventDefault();
        let LicenceCode = ctx.formFields['licenseCode'];
        setError('');
        setSuccess('');
        const user = {
            'action': 'mfwp_membership_validate_license_key',
            nonce: frontend_ajax_object.wps_standard_nonce,   // pass the nonce here
            purchase_code: LicenceCode,
        };
        axios.post(frontend_ajax_object.ajaxurl, qs.stringify(user) )
        .then(res => {
           
            console.log( res.data );
            if (res.data.status == true) {
                setSuccess(res.data.msg);
               
            } else {
                setError(res.data.msg);
              
            }
        }).catch(er=>{
            setError(er);
            console.log(er);
    })
    }
 
    const ctx = useContext(Context)
    return (
        <Context.Provider value={{
           error : error,
           success:success,
        }}>
        <Fragment>
            <FormControl component="fieldset" fullWidth className="fieldsetWrapper">
            <FormLabel component="legend" className="wpsFormLabel">{ __('Bingo! You are all set to take advantage of your business. Lastly, we urge you to allow us collect some','subscriptions-for-woocommerce')} <a href='https://wpswings.com/plugin-usage-tracking/' target="_blank" >{__('information','subscriptions-for-woocommerce') }</a> { __( 'in order to improve this plugin and provide better support. If you want, you can dis-allow anytime settings, We never track down your personal data. Promise!', 'membership-for-woocommerce' ) }
                </FormLabel>
                <RadioGroup aria-label="gender" name="consetCheck" value={ctx.formFields['consetCheck']} onChange={ctx.changeHandler} className={classes.margin}>
                    <FormControlLabel value="yes" control={<Radio color="primary"/>} label="Yes" className="wpsFormRadio"/>
                    <FormControlLabel value="no" control={<Radio color="primary"/>} label="No" className="wpsFormRadio"/>
                </RadioGroup>
            </FormControl>
            <FormControl component="fieldset" fullWidth className="fieldsetWrapper">
            {(() => {
     
                if (frontend_ajax_object.is_pro_plugin == 'true') {
                     return (
                      <TextField 
                        value={ctx.formFields['licenseCode']}
                        onChange={ctx.changeHandler} 
                        id="licenseCode" 
                        name="licenseCode" 
                        label={__('Enter your license code')}  variant="outlined" className={classes.margin}/>
                      
                        )
                }
          })()}
          
          <div id="div_licence">
  
            {(error) && <p className={classes.color}>{error}</p>}
            {(success) && <p className={classes.colorOne}>{success}</p>}</div>
               </FormControl>

                <FormControl component="fieldset" fullWidth className="fieldsetWrapper">
            {(() => {
     
                if (frontend_ajax_object.is_pro_plugin == 'true') {
                     return (
                         <div>
                        <div id="div_licence"></div>
                        <Button id="button_licence" value="buttomn" onClick={buttonclickhandler} fullWidth="100%" border="20%" theme="pink" color="black">{ __( 'Validate Licence Key', 'membership-for-woocommerce' ) }</Button>
               </div>
                        )
                }
          })()}
          
            </FormControl>
        </Fragment> 
        </Context.Provider>
    );

}
