/**
 * Import remote dependencies.
 */
import React from "react";
import { __ } from "@wordpress/i18n";
import { sprintf } from "sprintf-js";

/**
 * This component power the settings component.
 *
 * @since 1.0.0
 */

const SectionError = (props) => {
  const { sectionName, ...inputProps } = props;

  return (
    <div className="container">
      <div className="wpstorm-notify-error-container">
        <div className="error-header">{__("Warning!", "wpstorm-notify")}</div>
        <div className="error-body">
          <h5 className="error-title">
            {sprintf(__("%s  Attention Needed:", "wpstorm-notify"), sectionName)}
          </h5>
          <p className="error-text">
            {sprintf(
              __(
                "You have not checked %s in Integrations section. Please go first there and check %s usage toggle, Then come bake here.",
                "wpstorm-notify"
              ),
              sectionName,
              sectionName
            )}
          </p>
        </div>
      </div>
    </div>
  );
};

export default SectionError;
