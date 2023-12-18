import React from "react";
import { List } from 'react-content-loader';

function LoadingSpinner({props}) {
  return (
      <List
          height={300}
          width={1000}
          backgroundColor="#d9d9d9"
          foregroundColor="#ecebeb"
      />
  );
}

export default LoadingSpinner;

