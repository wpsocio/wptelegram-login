import React from 'react';
import { __ } from '../i18n';

export default ({result, type}) => (
  result ?
  <div className="mt-2">
    <span className="text-secondary">
      {__( 'Test Result:')}
    </span>
    {' '}
    <span className={`font-weight-bold text-${type}`}>
      {result}
    </span>
  </div> : null
);