import React from 'react';
import { Link } from 'react-router-dom';

// material-ui
import { Box, Typography, useTheme } from '@mui/material';

// assets
import { FiFacebook, FiLinkedin, FiInstagram, FiTwitter, FiYoutube } from 'react-icons/fi';

const socials = [
    <FiFacebook color="#0991FF" />,
    <FiLinkedin color="#0991FF" />,
    <FiInstagram color="#0991FF" />,
    <FiTwitter color="#0991FF" />,
    <FiYoutube color="#0991FF" />
];



const footerData = [{
    name: "About Us",
    path: '/about'
}, {
    name: 'Contact Us',
    path: '/contact-us'
}
, {
    name: 'Our Community Guidelines',
    path: '/#'
}, {
    name: 'Terms of Use',
    path: '/#'
}, {
    name: 'Terms of Service',
    path: '/#'
}, {
    name: 'Privacy Policy',
    path: '/privacy'
}, {
    name: 'FAQs',
    path: '/#'
}];

export const FooterSocials = (textAlign = 'end') => {
    const theme = useTheme();
    return (
        <Box
            sx={{
                width: '300px',
                height: '100px',
                '@media (max-width: 704px)': {
                    width: '100%'
                }
            }}
        >
            <Typography
                sx={{
                    ...theme.typography.subtitle1,
                    textAlign: textAlign,
                    '@media (max-width: 704px)': {
                        textAlign: 'center'
                    }
                }}
            >
                Follow Kokoruns
            </Typography>
            <Box
                sx={{
                    ...theme.typography.flex,
                    justifyContent: 'space-between',
                    marginTop: '20px',
                    '@media (max-width: 704px)': {
                        justifyContent: 'space-evenly'
                    }

                    //  cursor: 'pointer'
                }}
            >
                {socials.map((item) => item)}
            </Box>
        </Box>
    );
};

export default function Footer() {
    const theme = useTheme();
    return (
        <Box
            sx={{
                height: 'auto',
                background: '#faf9f9',
                padding: ' 50px calc((100vw - 1300px) / 2)',

                '@media (max-width: 1300px)': {
                    padding: '50px 20px 25px'
                }
            }}
        >
            <Box
                sx={{
                    display: 'flex',
                    '@media (max-width: 704px)': {
                        flexDirection: 'column'
                    }
                }}
            >
                <Box
                    component="img"
                    alt="kokoruns_logo"
                    sx={{
                        height: '40px',
                        width: '180px',
                        '@media (max-width: 600px)': {
                            height: '30px',
                            width: '130px'
                        }
                    }}
                    src="logo.png"
                />
                <Box
                    sx={{
                        display: 'flex',
                        paddingLeft: '30px',

                        flex: 1,

                        flexWrap: 'wrap',
                        marginTop: '-15px',
                        '@media (max-width: 1075px)': {
                            paddingLeft: '10px'
                        },
                        '@media (max-width: 940px)': {
                            paddingLeft: '10px',
                            paddingright: '10px'
                        },
                        '@media (max-width: 704px)': {
                            paddingLeft: '0px',
                            marginTop: '15px',
                            marginBottom: '25px'
                        }
                    }}
                >
                    {footerData.map((item) => (
                        <Typography
                            component={Link}
                            to={item.path}
                            sx={{
                                ...theme.typography.subtitle1,
                                marginTop: '15px',
                                minWidth: '200px',
                                color: 'inherit',
                                textDecoration: 'none',

                                '@media (max-width: 940px)': {
                                    minWidth: 'max-content',
                                    marginLeft: '15px'
                                },
                                '@media (max-width: 704px)': {
                                    width: '100%',
                                    marginLeft: '0px'
                                }
                            }}
                        >
                            {item.name}
                        </Typography>
                    ))}
                </Box>

                {/*                 

                <Box
                    sx={{
                        width: '300px',
                        height: '100px',
                        '@media (max-width: 704px)': {
                            width: '100%'
                        }
                    }}
                >
                    <Typography
                        sx={{
                            ...theme.typography.subtitle1,
                            textAlign: 'end',
                            '@media (max-width: 704px)': {
                                textAlign: 'center'
                            }
                        }}
                    >
                        Follow Kokoruns
                    </Typography>
                    <Box
                        sx={{
                            ...theme.typography.flex,
                            justifyContent: 'space-between',
                            marginTop: '20px',
                            '@media (max-width: 704px)': {
                                justifyContent: 'space-evenly'
                            }

                            //  cursor: 'pointer'
                        }}
                    >
                        {socials.map((item) => item)}
                    </Box>
                </Box>
 */}

                <FooterSocials />
            </Box>
            {/* lower */}
            <Box>
                <Box
                    component="hr"
                    sx={{
                        height: '1px',
                        background: 'black',
                        border: 'none',
                        marginTop: '25px',
                        '@media (max-width: 704px)': {
                            marginTop: '10px'
                        }
                    }}
                ></Box>
                <Typography style={{ textAlign: 'center', marginTop: '15px' }}>&copy; 2021. Kokoruns. All Right Reserved.</Typography>
            </Box>
        </Box>
    );
}
