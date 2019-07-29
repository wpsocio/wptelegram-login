/**
 * External dependencies
 */
import React from 'react';
// import { useState } from 'react';
/**
 * Internal dependencies
 */
import { Header, SettingsForm, Sidebar } from './components';

const App = () => {
	// const [formState, setFormState] = useState({});

	return (
		<div className="wrapper">
			<div className="content col-lg-9 col-md-9">
				<Header />
				<SettingsForm /* setFormState={setFormState} *//>
			</div>
			<div className="sidebar col-lg-3 col-md-3">
				{ /* <pre>{JSON.stringify(formState, null, 2)}</pre> */ }
				<Sidebar />
			</div>
		</div>
	);
};

export default App;
