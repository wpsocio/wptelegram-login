import React, { useState } from 'react';
import Header from './components/Header';
import SettingsForm from './components/SettingsForm';
import Sidebar from './components/Sidebar';
const App = () => {
  const [values, setValues] = useState({});

  return (
    <div className="wrapper">
      <div className="content col-lg-9 col-md-9">
        <Header />
        <SettingsForm setValues={setValues}/>
      </div>
      <div className="sidebar col-lg-3 col-md-3">
        <Sidebar />
          {/* <pre>{JSON.stringify(values, null, 2)}</pre> */}
          {/* <pre>{JSON.stringify(errors, null, 2)}</pre> */}
        {/* <SettingsForm /> */}
      </div>
    </div>
  );
}

export default App;
