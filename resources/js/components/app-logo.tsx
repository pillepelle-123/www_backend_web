import AppLogoIcon from './app-logo-icon';

export default function AppLogo() {
    return (
        <>
            {/* <div className="bg-sidebar-primary text-sidebar-primary-foreground flex aspect-square size-12 items-center justify-center rounded-md"> */}
            <div className="text-current dark:text-white flex flex-col aspect-square w-19 h-9 items-start justify-start rounded-md">
                <AppLogoIcon className="w-10 h-12" />
            </div>
            {/* <div className="ml-1 grid flex-1 text-left text-sm">
                <span className="mb-0.5 truncate leading-none font-semibold">Wer wirbt wen?</span>
            </div> */}
        </>
    );
}
