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

const SectionHeader = (props) => {
  const { sectionName } = props;

  return (
      <div className="projects-section-header">
        <p>{sectionName}</p>
      </div>
  );
};

export default SectionHeader;
