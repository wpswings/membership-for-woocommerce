import React,{useContext} from 'react';
import { TextField, FormControl, TextareaAutosize } from '@material-ui/core';
import { makeStyles } from '@material-ui/core/styles';
import Context from '../store/store';
import { __ } from '@wordpress/i18n';
const useStyles = makeStyles({
    margin: {
      marginBottom: '20px',
    },
});
const FirstStep = (props) => {
    const classes = useStyles();
    const ctx = useContext(Context);
    return(
        <> 
            <h3 className="mwb-title">{ __( 'Personal Detail', 'membership-for-woocommerce' ) }</h3>
            <FormControl component="fieldset" fullWidth className="fieldsetWrapper">
            <TextField 
                value={ctx.formFields['firstName']}
                onChange={ctx.changeHandler} 
                id="firstName" 
                name="firstName" 
                label="First Name"  variant="outlined" className={classes.margin}/>
            <TextField 
                value={ctx.formFields['email']}
                onChange={ctx.changeHandler}
                id="email" 
                name="email" 
                label="Email" variant="outlined" className={classes.margin}/>
            <TextareaAutosize
                 name="desc" 
                 value={ctx.formFields['desc']}
                 onChange={ctx.changeHandler}
                 aria-label="minimum height" 
                 minRows={4} placeholder="Minimum 3 rows"  
                 variant="outlined" className={classes.margin}/>
            </FormControl>
        </>
    )
}
export default FirstStep;