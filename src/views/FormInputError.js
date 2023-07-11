/**
 * Import remote dependencies.
 */
import React from "react";
import { CSSTransition } from "react-transition-group";
import { __ } from "@wordpress/i18n";

/**
 * This component power the settings component.
 *
 * @since 1.0.0
 */

const FormInputError = (props) => {
  const { errorMessage, hasErrors, ...inputProps } = props;

  return (
    <CSSTransition
      in={hasErrors}
      timeout={330}
      classNames="liveValidateMessage"
      unmountOnExit
    >
      <div className="alert alert-danger small liveValidateMessage">
        {errorMessage}
      </div>
    </CSSTransition>
  );
};

export default FormInputError;
