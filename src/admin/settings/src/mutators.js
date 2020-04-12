/* React Final Form mutator function */
export const updateFieldValue = ([name, value], state, { changeValue }) => {
	changeValue(state, name, () => value);
};
