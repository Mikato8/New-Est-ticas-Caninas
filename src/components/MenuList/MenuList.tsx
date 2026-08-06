import { Link } from "react-router-dom";

interface props {
    link: string,
    title: string
}
function MenuList({ link, title }: props) {
    return (
        <>
            <li className="list-unstyled">
                <Link className="text-white text-decoration-none" to={link}>
                    {title}
                </Link>
            </li>
        </>
    );
}
export default MenuList;