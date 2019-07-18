import React from 'react';

export default ({children}) => {
  return (
    <code style={{
      whiteSpace: 'pre-wrap',
      backgroundColor: '#fdfdfe'
    }}>
      {children}
    </code>
  );
}