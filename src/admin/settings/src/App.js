import React, { useState } from 'react';

import { Header, SettingsForm, Sidebar } from './components';

const App = () => {
	const [formState, setFormState] = useState({});

	const isDev = !process.env.NODE_ENV || process.env.NODE_ENV === 'development';

	return (
		<div className="wrapper">
			<div className="content col-lg-9 col-md-9">
				<Header />
				<SettingsForm setFormState={setFormState} />
			</div>
			<div className="sidebar col-lg-3 col-md-3">
				{isDev && <pre>{JSON.stringify(formState, null, 2)}</pre>}
				<Sidebar />
			</div>
		</div>
	);
};

export default App;
