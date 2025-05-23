import * as actionType from '../actionTypes/authActionsTypes';
import { REHYDRATE } from 'redux-persist';
import getCookie from '../../helpers/getCookie.js';

const initialState = {
    loading: false,
    user: null,
    error: '',
    token: '',
    active: null,
    authenticated: false,
    updated: false,
    user_id: null
};

export default function authReducer(state = initialState, action) {
    if (action.type === REHYDRATE) {
        const cookie = getCookie(action.payload?.authReducer.token);
        if (!cookie) {
            return { ...initialState };
        }

        return { ...action.payload.authReducer };
    } else if (action.type === actionType.LOGIN_BEGIN) {
        return { ...state, loading: true };
    } else if (action.type === actionType.ON_LOGIN_ERROR) {
        return { ...initialState, error: action.payload.error, loading: false };
    } else if (action.type === actionType.ON_LOGIN_SUCCESS) {
        return {
            ...state,
            loading: false,
            token: action.payload.access_token,
            active: action.payload.active,
            error: '',
            user_id: action.payload.user_id
        };
    } else if (action.type === actionType.ON_SIGNUP_SUCCESS) {
        return {
            ...initialState,
            loading: false,
            user_id: action.payload.user.user_id,
            error: ''
        };
    } else if (action.type === actionType.ON_SIGNUP_ERROR) {
        return {
            ...initialState,
            loading: false,
            error: action.payload.error
        };
    } else if (action.type === actionType.SESSION_EXPIRED) {
        return { ...initialState };
    } else if (action.type === actionType.ADD_USER_DETAILS) {
        return { ...state, user: action.payload, authenticated: true, active: 1 };
    } else if (action.type === actionType.SIGN_OUT) {
        return { ...initialState };
    } else if (action.type === actionType.USER_UPDATE) {
        return {
            ...state,
            loading: false,
            active: action.payload.updated
        };
    } else if (action.type === actionType.ON_UPDATE_SUCCESS) {
        return { ...state, updated: false };
    } else if (action.type === actionType.ON_UPDATE_ERROR) {
        return { ...state, error: action.payload.error };
    } else {
        return state;
    }
}
