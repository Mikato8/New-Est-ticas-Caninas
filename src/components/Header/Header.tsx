
interface props {
    title: string
}
function Header({ title }: props) {
    return (
        <>
            <nav className="navbar bg-dark border-bottom border-body" data-bs-theme="dark">
                <h2 className="text-white m-4 mt-2 mb-2">{title}</h2>
            </nav>
        </>
    );
}

export default Header;